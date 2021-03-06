<?php

namespace PandaLove\Http\Controllers\Overwatch;

use Illuminate\Http\Request as Request;
use Illuminate\Routing\Redirector as Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\View\Factory as View;
use Onyx\Account;
use Onyx\Destiny\Helpers\String\Text;
use Onyx\Overwatch\Client;
use Onyx\Overwatch\Helpers\Bot\MessageGenerator;
use Onyx\User;
use Onyx\XboxLive\Enums\Console;
use PandaLove\Commands\UpdateOverwatchAccount;
use PandaLove\Http\Controllers\Controller;

class ApiV1Controller extends Controller
{
    private $view;
    private $request;
    private $redirect;

    public $inactiveCounter = 10;

    protected $layout = 'layouts.master';

    public function __construct(View $view, Redirect $redirect, Request $request)
    {
        parent::__construct();
        $this->view = $view;
        $this->request = $request;
        $this->redirect = $redirect;
    }

    //---------------------------------------------------------------------------------
    // Overwatch GET
    //---------------------------------------------------------------------------------

    //---------------------------------------------------------------------------------
    // Overwatch POST
    //---------------------------------------------------------------------------------

    public function postUpdate()
    {
        $all = $this->request->all();

        if (isset($all['google_id']) && isset($all['gamertag']) && $all['gamertag'] == '') {
            try {
                /** @var User $user */
                $user = User::where('google_id', $all['google_id'])
                    ->firstOrFail();

                if ($user->account_id != 0 && $user->account->overwatch !== null) {
                    $old = clone $user->account->overwatch;

                    $this->dispatch(new UpdateOverwatchAccount($user->account));

                    /** @var Account $account */
                    $account = Account::where('seo', $user->account->seo)
                        ->with('overwatch')
                        ->where('accountType', $user->account->accountType)
                        ->first();

                    $new = $account->overwatch;

                    $msg = MessageGenerator::buildOverwatchUpdateMessage($user->account, $old, $new, $all['character'] ?? 'unknown');

                    return Response::json([
                        'error' => false,
                        'msg'   => $msg,
                    ], 200);
                } else {
                    $client = new Client();

                    /* @var Account $account */
                    $client->getAccountByTag($user->account->seo, $user->account->accountType);

                    return Response::json([
                        'error' => false,
                        'msg'   => 'First time entry. Command will work next time. Stats being created.',
                    ], 200);
                }
            } catch (\Exception $e) {
                return $this->_error($e->getMessage());
            }
        }

        if (isset($all['gamertag'])) {
            $platform = isset($all['platform']) ? $all['platform'] : Console::Xbox;

            $client = new Client();

            try {
                /* @var Account $account */
                $client->getAccountByTag($all['gamertag'], $platform);

                $account = Account::where('seo', Text::seoGamertag($all['gamertag']))
                    ->with('overwatch')
                    ->where('accountType', $platform)
                    ->first();

                $old = $account->overwatch;
                $this->dispatch(new UpdateOverwatchAccount($account));

                /** @var Account $account */
                $account = Account::where('seo', $account->seo)
                    ->with('overwatch')
                    ->where('accountType', $account->accountType)
                    ->first();

                $new = $account->overwatch;

                $msg = MessageGenerator::buildOverwatchUpdateMessage($account, $old, $new, $all['character'] ?? 'unknown');

                return Response::json([
                    'error' => false,
                    'msg'   => $msg,
                ], 200);
            } catch (\Exception $ex) {
                return $this->_error('Account could not be found - '.$all['gamertag']);
            }
        }
    }

    //---------------------------------------------------------------------------------
    // XPrivate Functions
    //---------------------------------------------------------------------------------

    private function _error($message)
    {
        return Response::json([
            'error'   => true,
            'message' => $message,
        ], 200);
    }
}
