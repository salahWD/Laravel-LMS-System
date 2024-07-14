<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Google_Service_Calendar_ConferenceData;
use Google_Service_Calendar_CreateConferenceRequest;
use Google_Service_Calendar_ConferenceSolutionKey;
use Google_Service_Calendar_ConferenceDataCreateRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Models\GoogleCalendarToken;

class GoogleCalendarController extends Controller {

  public function getClient() {
    $client = new Google_Client();
    $client->setApplicationName(env('APP_NAME'));
    $client->setScopes(Google_Service_Calendar::CALENDAR);
    $client->setAuthConfig(storage_path('app/google-calendar/calendar-credentials.json'));
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');
    $client->setRedirectUri(env('GOOGLE_CALENDAR_REDIRECT_URI'));

    $tokenData = GoogleCalendarToken::where('user_id', auth()->user()->id)->first();

    if ($tokenData) {

      $accessToken = json_decode($tokenData->token, true);
      $client->setAccessToken($accessToken);

      if ($client->isAccessTokenExpired()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        $tokenData->token = json_encode($client->getAccessToken());
        $tokenData->save();
      }
    } else {
      Log::info('No token found, redirecting to auth URL...');
      $authUrl = $client->createAuthUrl();
      return redirect()->away($authUrl);
    }

    return $client;
  }

  public function handleGoogleCallback(Request $request) {
    $authCode = $request->input('code');
    if (!$authCode) {
      dd("Authorization code not received. ", $authCode);
      return redirect()->route('profile.meetings')->with('error', 'Authorization code not received.');
    }

    try {
      $client = new Google_Client();
      $client->setAuthConfig(storage_path('app/google-calendar/calendar-credentials.json'));
      $client->setRedirectUri(env('GOOGLE_CALENDAR_REDIRECT_URI'));

      $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
      if (isset($accessToken['error'])) {
        throw new \Exception($accessToken['error_description']);
      }

      $client->setAccessToken($accessToken);

      $tokenData = GoogleCalendarToken::updateOrCreate(
        ['user_id' => auth()->user()->id],
        ['token' => json_encode($client->getAccessToken())]
      );

      return redirect()->route('profile.meetings')->with('success', 'Google Calendar linked successfully.');
    } catch (\Exception $e) {
      Log::error('Error during OAuth callback: ' . $e->getMessage());

      return redirect()->route('profile.meetings')->with('error', 'Failed to link Google Calendar.');
    }
  }

  public function createGoogleCalendarEvent($startDateTime, $endDateTime, $summary, $description, $location) {
    $client = $this->getClient();
    if ($client instanceof \Illuminate\Http\RedirectResponse) {
      return $client;
    }

    $service = new Google_Service_Calendar($client);

    $event = new Google_Service_Calendar_Event([
      'summary' => $summary,
      'description' => $description,
      'location' => $location,
      'start' => new Google_Service_Calendar_EventDateTime([
        'dateTime' => $startDateTime,
        'timeZone' => 'Europe/Istanbul',
      ]),
      'end' => new Google_Service_Calendar_EventDateTime([
        'dateTime' => $endDateTime,
        'timeZone' => 'Europe/Istanbul',
      ]),
      'conferenceData' => new Google_Service_Calendar_ConferenceData([
        'createRequest' => new Google_Service_Calendar_CreateConferenceRequest([
          'conferenceSolutionKey' => new Google_Service_Calendar_ConferenceSolutionKey([
            'type' => 'hangoutsMeet',
          ]),
          'requestId' => Str::random(16), // Make sure this is unique
        ]),
      ]),
    ]);

    $calendarId = 'primary';
    try {
      $event = $service->events->insert($calendarId, $event, ['conferenceDataVersion' => 1]);
      return $event->htmlLink; // Return the event link
    } catch (\Exception $e) {
      Log::error('Error creating Google Calendar event: ' . $e->getMessage());
      return null;
    }
  }

  // public function redirectToGoogle() {
  //   $client = new Google_Client();
  //   $client->setApplicationName(env('APP_NAME'));
  //   $client->setScopes(Google_Service_Calendar::CALENDAR);
  //   $client->setAuthConfig(storage_path('app/google-calendar/calendar-credentials.json'));
  //   $client->setAccessType('offline');
  //   $client->setPrompt('select_account consent');
  //   $client->setRedirectUri(env('GOOGLE_CALENDAR_REDIRECT_URI'));

  //   $authUrl = $client->createAuthUrl();
  //   return redirect()->away($authUrl);
  // }
}
