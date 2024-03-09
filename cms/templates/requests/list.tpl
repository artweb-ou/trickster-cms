<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Requests debug</title>
    <style>
        .requests-table td {
            padding: 5px 8px;
            white-space: nowrap;
            font-size: 14px;
        }
    </style>
</head>
<body>
<h2>Top {count($topIpCount)} IPs by Request Count</h2>
<ul>
    {foreach $topIpCount as $ip => $count}
        <li>{$ip} -> {$count}</li>
    {/foreach}
</ul>

<h2>Top {count($topIpDuration)} IPs by Total Duration</h2>
<ul>
    {foreach $topIpDuration as $ip => $duration}
        <li>{$ip} -> {$duration}</li>
    {/foreach}
</ul>
<h2>Top {count($topLongestRequests)} Longest Requests</h2>
<table class="requests-table">
    <thead>
    <tr>
        <th>Start Time</th>
        <th>Duration</th>
        <th>IP</th>
        <th>URL</th>
        <th>User Agent</th>
    </tr>
    </thead>
    <tbody>
    {foreach $topLongestRequests as $request}
        <tr>
            <td>{$request->formattedStartTime}</td>
            <td>{$request->formattedDuration}</td>
            <td><a href="https://ipinfo.io/{$request->ip}" target="_blank">{$request->ip}</a></td>
            <td><a href="{$request->url}" target="_blank">{$request->url}</a></td>
            <td>{$request->userAgent}</td>
        </tr>
    {/foreach}
    </tbody>
</table>
<h2>All Requests</h2>
<table class="requests-table">
    <thead>
    <tr>
        <th>Start Time</th>
        <th>Duration</th>
        <th>IP</th>
        <th>URL</th>
        <th>User Agent</th>
    </tr>
    </thead>
    <tbody>
    {foreach $requests as $request}
        <tr>
            <td>{$request->formattedStartTime}</td>
            <td>{$request->formattedDuration}</td>
            <td><a href="https://ipinfo.io/{$request->ip}" target="_blank">{$request->ip}</a></td>
            <td><a href="{$request->url}" target="_blank">{$request->url}</a></td>
            <td>{$request->userAgent}</td>
        </tr>
    {/foreach}
    </tbody>
</table>
</body>
</html>
