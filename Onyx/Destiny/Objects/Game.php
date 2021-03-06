<?php

namespace Onyx\Destiny\Objects;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Onyx\Destiny\Helpers\Assets\Images;
use Onyx\Destiny\Helpers\String\Hashes;
use Onyx\Destiny\Helpers\String\Text;
use Onyx\User;

/**
 * Class Game.
 *
 * @property int $id
 * @property int $instancId
 * @property int $referenceId
 * @property bool $isHard
 * @property string $type Raid|Flawless|PVP|PoE|ToO
 * @property Carbon $occurredAt
 * @property int $raidTuesday
 * @property int $timeTookInSeconds
 * @property int $passageId
 * @property bool $hidden
 * @property int $version
 * @property bool $mercy
 */
class Game extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'destiny_games';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var \Onyx\Destiny\Helpers\String\Hashes
     */
    private $translator;

    public function __construct()
    {
        parent::__construct();

        $this->translator = new Hashes();
    }

    public static function boot()
    {
        parent::boot();

        self::deleting(function ($game) {
            foreach ($game->players as $player) {
                $player->delete();
            }

            $game->comments()->delete();

            if ($game->pvp instanceof PVP) {
                $game->pvp->delete();
            }
        });
    }

    //---------------------------------------------------------------------------------
    // Accessors & Mutators
    //---------------------------------------------------------------------------------

    public function setReferenceIdAttribute($value)
    {
        $this->setAttributePullImage('referenceId', $value);
        $object = $this->translator->map($value, false);

        $hard = false;
        if (str_contains($object->title, 'Crota')) {
            if ($object->extraThird == 33) {
                $hard = true;
            }
        } elseif (str_contains($object->title, 'Vault')) {
            if ($object->extraThird == 30) {
                $hard = true;
            }
        } elseif (str_contains($object->title, 'King')) {
            if ($object->extraThird == 42) {
                $hard = true;
            }
        }

        $this->attributes['isHard'] = boolval($hard);
    }

    public function setOccurredAtAttribute($value)
    {
        $this->attributes['occurredAt'] = new Carbon($value, 'America/Chicago');
    }

    public function getIsHardAttribute($value)
    {
        return boolval($value);
    }

    public function getOccurredAtAttribute($value)
    {
        $date = new Carbon($value);

        if ($date->diffInDays() > 30) {
            return $date->format('M j, Y - g:ma');
        } else {
            return $date->diffForHumans();
        }
    }

    public function getTimeTookInSecondsAttribute($value)
    {
        return Text::timeDuration($value);
    }

    //---------------------------------------------------------------------------------
    // Public Methods
    //---------------------------------------------------------------------------------

    public function players()
    {
        return $this->hasMany('Onyx\Destiny\Objects\GamePlayer', 'game_id', 'instanceId');
    }

    public function pvp()
    {
        return $this->hasOne('Onyx\Destiny\Objects\PVP', 'instanceId', 'instanceId');
    }

    public function teamPlayers($team_id)
    {
        $players = $this->players->filter(function ($player) use ($team_id) {
            return $player->team == $team_id;
        });

        return $players;
    }

    public function pandas()
    {
        return $this->players->reject(function ($player) {
            return !$player->account->isPandaLove();
        });
    }

    public function comments()
    {
        return $this->morphMany('Onyx\Objects\Comment', 'commentable')
            ->where('parent_comment_id', 0)
            ->orderBy('created_at', 'DESC');
    }

    public function findAccountViaMembershipId($membershipId, $returnAccount = true)
    {
        foreach ($this->players as $player) {
            if ($player->membershipId == $membershipId) {
                if ($returnAccount == false) {
                    return $player;
                } else {
                    return $player->account;
                }
            }
        }

        $destiny = Data::where('membershipId', $membershipId)->first();
        if ($destiny instanceof Data) {
            if ($returnAccount) {
                return $destiny->account;
            }
        }
    }

    public function completed()
    {
        $count = 0;
        foreach ($this->getRelation('players') as $player) {
            if ($player->completed && $player->historyAccount != null) {
                if ($player->historyAccount->user instanceof User && $player->historyAccount->user->isPanda) {
                    $count++;
                }
            }
        }

        return $count;
    }

    public function setTranslatorUrl($url)
    {
        $this->translator->setUrl($url);
    }

    public function getTranslatorUrl()
    {
        return $this->translator->getUrl();
    }

    public function scopeSingular($query)
    {
        return $query
            ->with('players.account.user')
            ->where('raidTuesday', 0)
            ->orderBy('occurredAt', 'DESC');
    }

    public function scopeOfTuesday($query, $value)
    {
        return $query->where('raidTuesday', $value)->orderBy('occurredAt', 'DESC');
    }

    public function scopeOfPassage($query, $value)
    {
        return $query->where('passageId', $value)->orderBy('occurredAt', 'ASC');
    }

    public function scopeRaid($query, $p)
    {
        if ($p) {
            return $query->where('type', 'Raid');
        }

        return $query->where('type', 'Raid')->where('hidden', false);
    }

    public function scopeToO($query)
    {
        return $query->where('type', 'ToO');
    }

    public function scopeFlawless($query, $p)
    {
        if ($p) {
            return $query->where('type', 'Flawless');
        }

        return $query->where('type', 'Flawless')->where('hidden', false);
    }

    public function scopeTuesday($query, $p)
    {
        return $this->scopeRaid($query, $p)
            ->selectRaw('*, count(*) as raidCount, sum(timeTookInSeconds) as totalTime')
            ->groupBy('raidTuesday')
            ->orderBy('occurredAt', 'DESC')
            ->having('raidTuesday', '>', 0);
    }

    public function scopeMultiplayer($query, $p)
    {
        if ($p) {
            return $query->where('type', 'PVP');
        }

        return $query->where('type', 'PVP')->where('hidden', false);
    }

    public function scopePoE($query, $p)
    {
        if ($p) {
            return $query->where('type', 'PoE');
        }

        return $query->where('type', 'PoE')->where('hidden', false);
    }

    public function scopePassage($query)
    {
        return $this->scopeToO($query)
            ->selectRaw('*, count(*) as gameCount, sum(timeTookInSeconds) as totalTime, GROUP_CONCAT(referenceId) as maps')
            ->groupBy('passageId')
            ->orderBy('occurredAt', 'DESC')
            ->having('passageId', '>', 0);
    }

    public function type()
    {
        return $this->translator->map($this->referenceId, false);
    }

    public function getRawSeconds()
    {
        return $this->attributes['timeTookInSeconds'];
    }

    public function isPoE()
    {
        return $this->type == 'PoE';
    }

    public function isToO()
    {
        return $this->type == 'ToO';
    }

    public function title()
    {
        switch ($this->type) {
            case 'PoE':
                return '<span class="ui purple label">Prison Of Elders</span>';

            case 'ToO':
                return '<span class="ui black label">Trials Of Osiris</span>';

            case 'PVP':
                return '<span class="ui red label">PVP</span>';

            case 'Raid':
            case 'Flawless':
                return '<span class="ui label">Raid</span>';
        }
    }

    public function buildUrl()
    {
        switch ($this->type) {
            case 'PoE':
            case 'PVP':
            case 'Flawless':
                return \URL::action('Destiny\GameController@getGame', [$this->instanceId]);

            case 'ToO':
                return \URL::action('Destiny\GameController@getPassage', [$this->passageId, $this->instanceId]);

            case 'Raid':
                if ($this->raidTuesday != 0) {
                    return \URL::action('Destiny\GameController@getTuesday', [$this->raidTuesday, $this->instanceId]);
                } else {
                    return \URL::action('Destiny\GameController@getGame', [$this->instanceId]);
                }
                break;

            default:
                return '#';
        }
    }

    //---------------------------------------------------------------------------------
    // Private Methods
    //---------------------------------------------------------------------------------

    /**
     * @param string $index Index for $this->attributes
     * @param string $hash  hashCode for item
     *
     * @throws \Onyx\Destiny\Helpers\String\HashNotLocatedException
     */
    private function setAttributePullImage($index, $hash)
    {
        if ($hash == null || $hash == '') {
            return;
        }
        Images::saveImagesLocally($this->translator->map($hash, false));
        $this->attributes[$index] = $hash;
    }
}
