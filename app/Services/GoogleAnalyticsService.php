<?php

namespace App\Services;

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\OrderBy;
use Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy;


class GoogleAnalyticsService {
  protected static function tsum($item) {
    return $item->getMetricValues()[0]->getValue();
  }
  protected static function getClient() {
    return new BetaAnalyticsDataClient([
      'credentials' => config('analytics.service_account_credentials_json')
    ]);
  }

  protected static function getPropertyId() {
    return 'properties/' . config('analytics.property_id');
  }

  public static function getTotalUsers() {
    $client = self::getClient();
    $propertyId = self::getPropertyId();

    $response = $client->runReport([
      'property' => $propertyId,
      'dateRanges' => [new DateRange(['start_date' => '30daysAgo', 'end_date' => 'today'])],
      'metrics' => [new Metric(['name' => 'totalUsers'])]
    ]);

    return $response->getRows()[0]->getMetricValues()[0]->getValue();
  }

  public static function getTopCountries() {
    $client = self::getClient();
    $propertyId = self::getPropertyId();

    $response = $client->runReport([
      'property' => $propertyId,
      'dateRanges' => [new DateRange(['start_date' => '30daysAgo', 'end_date' => 'today'])],
      'dimensions' => [new Dimension(['name' => 'country'])],
      'metrics' => [new Metric(['name' => 'sessions'])],
      'orderBys' => [
        new OrderBy([
          'metric' => new MetricOrderBy(['metric_name' => 'sessions']),
          'desc' => true
        ]),
      ],
      'limit' => 5,
    ]);


    $countries = [];
    $totalSessions = 0;
    foreach ($response->getRows() as $row) {
      $totalSessions += $row->getMetricValues()[0]->getValue();
    }

    foreach ($response->getRows() as $row) {
      $country = $row->getDimensionValues()[0]->getValue();
      $sessions = $row->getMetricValues()[0]->getValue();
      $percentage = ($sessions / $totalSessions) * 100;
      $countries[] = ['country' => $country, 'percentage' => round($percentage, 2)];
    }

    return $countries;
  }

  public static function getTopPages() {
    $client = self::getClient();
    $propertyId = self::getPropertyId();

    $response = $client->runReport([
      'property' => $propertyId,
      'dateRanges' => [new DateRange(['start_date' => '30daysAgo', 'end_date' => 'today'])],
      'dimensions' => [new Dimension(['name' => 'pagePath'])],
      'metrics' => [
        new Metric(['name' => 'screenPageViews']),
        new Metric(['name' => 'averageSessionDuration']),
        new Metric(['name' => 'bounceRate']),
      ],
      'orderBys' => [
        new OrderBy([
          'metric' => new MetricOrderBy(['metric_name' => 'screenPageViews']),
          'desc' => true
        ])
      ],
      'limit' => 5,
    ]);

    $pages = [];
    foreach ($response->getRows() as $row) {
      $pagePath = $row->getDimensionValues()[0]->getValue();
      $pageViews = $row->getMetricValues()[0]->getValue();
      $avgTimeSpent = $row->getMetricValues()[1]->getValue();
      $bounceRate = $row->getMetricValues()[2]->getValue();
      $pages[] = [
        'pagePath' => $pagePath,
        'pageViews' => $pageViews,
        'avgTimeSpent' => round($avgTimeSpent, 2),
        'bounceRate' => round($bounceRate, 2)
      ];
    }

    return $pages;
  }

  public static function getTotalBounceRate() {
    $client = self::getClient();
    $propertyId = self::getPropertyId();

    $response = $client->runReport([
      'property' => $propertyId,
      'dateRanges' => [new DateRange(['start_date' => '30daysAgo', 'end_date' => 'today'])],
      'metrics' => [new Metric(['name' => 'bounceRate'])]
    ]);

    return round($response->getRows()[0]->getMetricValues()[0]->getValue(), 2);
  }

  public static function getDailyTrafficLastMonth() {
    $client = self::getClient();
    $propertyId = self::getPropertyId();

    $response = $client->runReport([
      'property' => $propertyId,
      'dateRanges' => [new DateRange(['start_date' => '30daysAgo', 'end_date' => 'today'])],
      'dimensions' => [new Dimension(['name' => 'date'])],
      'metrics' => [new Metric(['name' => 'sessions'])],
      // 'orderBys' => [
      //   new OrderBy([
      //     'metric' => new MetricOrderBy(['metric_name' => 'date']),
      //     'desc' => true
      //   ])
      // ]
    ]);

    $trafficData = [];
    foreach ($response->getRows() as $row) {
      $date = $row->getDimensionValues()[0]->getValue();
      $sessions = $row->getMetricValues()[0]->getValue();
      $trafficData[] = ['date' => $date, 'sessions' => $sessions];
    }

    return $trafficData;
  }
}
