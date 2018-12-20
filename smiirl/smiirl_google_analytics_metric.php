<?php
// https://developers.google.com/analytics/devguides/reporting/core/v4/quickstart/service-php
// Follor those instructions to install google api client with composer and to create Google project and serivce account
// Remeber to add the generated email as a user to your Google Anatics property

require_once __DIR__ . '/vendor/autoload.php';

$analytics = initializeAnalytics();
$reports = getReports($analytics);
//print(json_encode($reports)); // useful for debugging

$number = parseNumber($reports);
print '{"number": ' . $number . '}';

function initializeAnalytics()
{
    // replace XXX with the name of the file downloaded from Google when creating the service account
    // file moved to parent folder of web root so not publicly accessible via web
    $KEY_FILE_LOCATION = dirname(__DIR__) . '/XXX.json';

    // Create and configure a new client object.
    $client = new Google_Client();
    $client->setApplicationName("Smiirl Counter");
    $client->setAuthConfig($KEY_FILE_LOCATION);
    $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
    $analytics = new Google_Service_AnalyticsReporting($client);

    return $analytics;
}

function getReports($analytics) {
    // Replace XXXwith your Google Analytics view ID
    // https://ga-dev-tools.appspot.com/account-explorer/
    $VIEW_ID = "XXX";

    // Create the DateRange object.
    $dateRange = new Google_Service_AnalyticsReporting_DateRange();
    $dateRange->setStartDate("today");
    $dateRange->setEndDate("today");

    // Create the Metrics object.
    $metric = new Google_Service_AnalyticsReporting_Metric();
    $metric->setExpression("ga:users");
    $metric->setAlias("users");

    // Create the ReportRequest object.
    $request = new Google_Service_AnalyticsReporting_ReportRequest();
    $request->setViewId($VIEW_ID);
    $request->setDateRanges($dateRange);
    $request->setMetrics(array($metric));

    $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
    $body->setReportRequests(array($request));

    return $analytics->reports->batchGet($body);
}

function parseNumber($reports) {
    // only supports one metric (duh)
    $report = $reports[0];
    $rows = $report->getData()->getRows();
    $row = $rows[0];
    $metrics = $row->getMetrics();
    $values = $metrics[0]->getValues();
    $value = $values[0];

    return $value;
}