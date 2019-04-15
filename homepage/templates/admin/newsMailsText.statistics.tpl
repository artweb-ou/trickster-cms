<div class="newsmail_base_statistics_container">
	{stripdomspaces}
	{$roundValue = 2}
		<div class="newsmail_statistics_table_container panel_component">
			<div class="panel_heading">
				{translations name="newsmail.statistics_base_table_heading"}
			</div>
			<div class="panel_content">
				<table class="newsmail_statistics_table table_component">
					<thead>
					<tr>
						<th>

						</th>
						<th class="newsmail_statistics_table_value">
							{translations name="newsmail.statistics_members"}
						</th>
						<th class="newsmail_statistics_table_value">
							{translations name="newsmail.statistics_percentage_of_members"}
						</th>
						<th class="newsmail_statistics_table_value">
							{translations name="newsmail.statistics_total_clicks"}
						</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<th>{translations name="newsmail.total"}</th>
						<td>{$element->dispatchmentsInfo['total']}</td>
						<td>100%</td>
						<td></td>
					</tr>
					<tr>
						<th>{translations name="newsmail.success"}</th>
						<td>{$element->dispatchmentsInfo['success']}</td>
						<td>{round($element->dispatchmentsInfo['success'] / $element->dispatchmentsInfo['total'] * 100, $roundValue)}%</td>
						<td></td>
					</tr>
					<tr>
						<th>{translations name="newsmail.fail"}</th>
						<td>{$element->dispatchmentsInfo['fail']}</td>
						<td>{round($element->dispatchmentsInfo['fail'] / $element->dispatchmentsInfo['total'] * 100, $roundValue)}%</td>
						<td></td>
					</tr>
					<tr>
						<th>{translations name="newsmail.awaiting"}</th>
						<td>{$element->dispatchmentsInfo['awaiting']}</td>
						<td>{round($element->dispatchmentsInfo['awaiting'] / $element->dispatchmentsInfo['total'] * 100, $roundValue)}%</td>
						<td></td>
					</tr>
					{foreach $element->dispatchmentEventsInfo as $event}
						<tr>
							<th>{translations name="newsmail.{$event@key}"}</th>
							<td>{count($event['users'])}</td>
							<td>{round(count($event['users']) / $element->dispatchmentsInfo['total'] * 100, $roundValue)}%</td>
							<td>{$event['clicks']}</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
		</div>
		<div class="newsmails_statistics_base_funnel_container panel_component">
			{$emailsOpenedPercentage = round(count($element->dispatchmentEventsInfo['newsMail_emailOpened']['users']) / $element->dispatchmentsInfo['total'] * 100, $roundValue)}
			{$linkClickedPercentage = round(count($element->userClickedLinksCombined) / $element->dispatchmentsInfo['total'] * 100, $roundValue)}
			{*{$emailsOpenedPercentage = 66.6}*}
			{*{$linkClickedPercentage = 60.6}*}

			{$totalWidth = 500}
			{$totalHeight = 250}

			{$funnelLeft = 0}
			{$funnelWidth = $totalWidth - $funnelLeft}
			{$funnelCenter = $funnelLeft + $funnelWidth/2}
			{$funnelRight = $totalWidth}

			{$pos1 = ($funnelWidth - $funnelWidth*$emailsOpenedPercentage/100)/2}
			{$pos1Right = $totalWidth - $pos1}
			{$pos1Left = $funnelLeft + $pos1}

			{$textPadding = 50}
			{$polygonHeight = 60}
			{$textHeight = 14}
			{$pos2 = ($funnelWidth - $funnelWidth*$linkClickedPercentage/100)/2}
			{$pos2Right = $totalWidth - $pos2}
			{$pos2Left = $funnelLeft + $pos2}
			{$linksHeight = $polygonHeight}
			{$textTop = $polygonHeight/2}

			<div class="panel_content">
				<svg class="newsmails_funnel_svg" viewBox="0 0 {$totalWidth} {$totalHeight}">
					<g>
						<polygon points="{$funnelLeft},0 {$funnelRight},0 {$pos1Right},{$polygonHeight} {$pos1Left},{$polygonHeight}" style="fill:#438ac8;stroke:#ffffff;stroke-width:1" />
						<text x="{$funnelCenter}" y="{$textTop-$textHeight*1.2}" dominant-baseline="middle" text-anchor="middle" font-weight="bold" font-size="{$textHeight}px" fill="#485573">{translations name="newsmail.total"}</text>
						<text x="{$funnelCenter}" text-anchor="middle" y="{$textTop}" dominant-baseline="middle" font-weight="bold" font-size="{$textHeight}px" fill="#485573">{$element->dispatchmentsInfo['total']} - 100%</text>
					</g>
					<g transform="translate(0, {$polygonHeight + 10})">
						{if $emailsOpenedPercentage}
							<polygon points="{$pos1Left},0 {$pos1Right},0 {$pos2Right},{$polygonHeight} {$pos2Left},{$polygonHeight}" style="fill:#5aca55;stroke:#ffffff;stroke-width:1" />
						{/if}
						<text x="{$funnelCenter}" y="{$textTop-$textHeight*1.2}" dominant-baseline="middle" text-anchor="middle" font-weight="bold" font-size="{$textHeight}px" fill="#485573">{translations name="newsmail.newsMail_emailOpened"}</text>
						<text x="{$funnelCenter}" text-anchor="middle" y="{$textTop}" dominant-baseline="middle" font-weight="bold" font-size="{$textHeight}px" fill="#485573">{count($element->dispatchmentEventsInfo['newsMail_emailOpened']['users'])} - {$emailsOpenedPercentage}%</text>
					</g>
					<g transform="translate(0, {($polygonHeight + 10) * 2})">
						{if $linkClickedPercentage}
							<polygon points="{$pos2Left},0 {$pos2Right},0 {$funnelCenter},{$linksHeight} {$funnelCenter},{$linksHeight}" style="fill:#e765d3;stroke:#ffffff;stroke-width:1" />
						{/if}
						<text x="{$funnelCenter}" y="{$textTop-$textHeight*1.2}" dominant-baseline="middle" text-anchor="middle" font-weight="bold" font-size="{$textHeight}px" fill="#485573">{translations name="newsmail.newsMail_linkClicked"}</text>
						<text x="{$funnelCenter}" text-anchor="middle" y="{$textTop}" dominant-baseline="middle" font-weight="bold" font-size="{$textHeight}px" fill="#485573">{count($element->userClickedLinksCombined)} - {$linkClickedPercentage}%</text>
					</g>
				</svg>
			</div>

		</div>
	{/stripdomspaces}
</div>

{$linkClickInfo = array('newsMail_linkClicked', 'newsMail_externalLinkClicked')}
<div class="newsmail_statistics_container">
	{foreach $linkClickInfo as $clickType}
		{$event = $element->dispatchmentEventsInfo[$clickType]}
		{if !empty($event)}
			<div class="panel_component">
				<div class="panel_heading">
					<h1 class="newsmail_statistics_heading bottom_heading">{translations name="newsmail.statistics_{$clickType}_heading"}</h1>
				</div>
				<div class="panel_content">
					<table class="table_component newsmail_statistics_links_tablenewsmail_statistics_links_table">
						<thead>
						<tr>
							<th>{translations name="newsmail.statistics_link"}</th>
							<th>{translations name="newsmail.statistics_members"}</th>
							<th>{translations name="newsmail.statistics_percentage_of_members"}</th>
							<th>{translations name="newsmail.statistics_total_clicks"}</th>
						</tr>
						</thead>
						<tbody>
						{foreach $event['links'] as $link}
							<tr>
								<td><a href="{$link@key}">{$link@key}</a></td>
								<td>{count($link['users'])}</td>
								<td>{round(count($link['users']) / count($event['users'])  * 100)}%</td>
								<td>
									{$link['clicks']}
								</td>
							</tr>
						{/foreach}
						</tbody>
					</table>
				</div>
			</div>
		{/if}
	{/foreach}
</div>
