<?php namespace Onyx\Halo5\Objects;

use Illuminate\Database\Eloquent\Model;
use Onyx\Account;
use Onyx\Halo5\Enums\DeathType;
use Onyx\Halo5\Enums\EventName;
use Onyx\Halo5\Helpers\Date\DateHelper;
use Ramsey\Uuid\Uuid;

/**
 * Class MatchEvent
 * @package Onyx\Halo5\Objects
 * @property string $uuid
 * @property string $game_id
 * @property integer $death_owner
 * @property integer $death_type
 * @property integer $killer_id
 * @property integer $killer_type
 * @property array $killer_attachments
 * @property integer $killer_weapon_id
 * @property double $killer_x
 * @property double $killer_y
 * @property double $killer_z
 * @property integer $victim_id
 * @property integer $victim_type
 * @property array $victim_attachments
 * @property integer $victim_weapon_id
 * @property double $victim_x
 * @property double $victim_y
 * @property double $victim_z
 * @property double $distance
 * @property integer $event_name
 * @property integer $seconds_since_start
 *
 * @property Account $killer
 * @property Account $victim
 * @property Weapon $killer_weapon
 * @property Weapon $victim_weapon
 */
class MatchEvent extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'halo5_match_events';

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['uuid'];

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * Disable timestamps
     *
     * @var bool
     */
    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($matchEvent)
        {
            $matchEvent->uuid = Uuid::uuid4();
            $matchEvent->setDistance();
        });
    }

    //---------------------------------------------------------------------------------
    // Accessors & Mutators
    //---------------------------------------------------------------------------------

    public function setKillerIdAttribute(Account $killer)
    {
        $this->attributes['killer_id'] = $killer->id;
    }

    public function setVictimIdAttribute(Account $victim)
    {
        $this->attributes['victim_id'] = $victim->id;
    }

    public function setDeathTypeAttribute(array $event)
    {
        $fields = ['IsAssassination', 'IsGroundPound', 'IsHeadshot',  'IsMelee', 'IsShoulderBash', 'IsWeapon'];

        foreach ($fields as $field)
        {
            if (isset($event[$field]) && $event[$field])
            {
                $this->attributes['death_type'] = DeathType::getId($field);
                break;
            }
        }
    }

    public function setKillerAttachmentsAttribute(array $value)
    {
        $this->attributes['killer_attachments'] = json_encode($value);
    }

    public function setVictimAttachmentsAttribute(array $value)
    {
        $this->attributes['victim_attachments'] = json_encode($value);
    }

    public function setEventNameAttribute($value)
    {
        $this->attributes['event_name'] = EventName::getId($value);
    }

    public function setSecondsSinceStartAttribute($value)
    {
        $this->attributes['seconds_since_start'] = DateHelper::returnSeconds($value);
    }

    public function setKillerWeaponIdAttribute($value)
    {
        $this->attributes['killer_weapon_id'] = ($value > 0) ? $value : null;
    }

    public function setVictimWeaponIdAttribute($value)
    {
        $this->attributes['victim_weapon_id'] = ($value > 0) ? $value : null;
    }

    public function getKillerAttachmentsAttribute($value)
    {
        return json_decode($value);
    }

    public function getVictimAttachmentsAttribute($value)
    {
        return json_decode($value);
    }

    public function getKillerXAttribute($value)
    {
        return floatval($value);
    }

    public function getKillerYAttribute($value)
    {
        return floatval($value);
    }

    public function getKillerZAttribute($value)
    {
        return floatval($value);
    }

    public function getVictimXAttribute($value)
    {
        return floatval($value);
    }

    public function getVictimYAttribute($value)
    {
        return floatval($value);
    }

    public function getVictimZAttribute($value)
    {
        return floatval($value);
    }

    public function getDistanceAttribute($value)
    {
        return floatval($value);
    }

    public function getSecondsSinceStartAttribute($value)
    {
        return intval($value);
    }

    //---------------------------------------------------------------------------------
    // Public Methods
    //---------------------------------------------------------------------------------

    /**
     *
     */
    public function setDistance()
    {
        $x = $this->victim_x - $this->killer_x;
        $y = $this->victim_y - $this->killer_y;
        $z = $this->victim_z - $this->killer_z;

        $this->attributes['distance'] = sqrt(pow($x, 2) + pow($y, 2) + pow($z, 2));
    }

    /**
     * @param string $type
     * @param array $data
     */
    public function setPoint($type = 'Killer', array $data)
    {
        $type = ($type == 'Killer' ? 'killer' : 'victim');

        $this->attributes[$type . "_x"] = floatval($data['x']);
        $this->attributes[$type . "_y"] = floatval($data['y']);
        $this->attributes[$type . "_z"] = floatval($data['z']);
    }

    public function match()
    {
        return $this->belongsTo('Onyx\Halo5\Objects\Match', 'game_id', 'uuid');
    }

    public function assists()
    {
        return $this->hasMany('Onyx\Halo5\Objects\MatchEventAssist', 'match_event', 'uuid');
    }

    public function killer()
    {
        return $this->belongsTo('Onyx\Account', 'killer_id', 'id')->select('gamertag', 'id', 'seo');
    }

    public function victim()
    {
        return $this->belongsTo('Onyx\Account', 'victim_id', 'id')->select('gamertag', 'id', 'seo');
    }

    public function killer_weapon()
    {
        return $this->belongsTo('Onyx\Halo5\Objects\Weapon', 'killer_weapon_id', 'uuid');
    }

    public function victim_weapon()
    {
        return $this->belongsTo('Onyx\Halo5\Objects\Weapon', 'victim_weapon_id', 'uuid');
    }
}