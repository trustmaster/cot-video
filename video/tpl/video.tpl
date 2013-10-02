<!-- BEGIN: MAIN -->

<div class="col first">
	<div class="block">
		<h2>{VIDEO_PATH}</h2>
		<!-- BEGIN: VIDEO_CATS -->
			<h3><a href="{VIDEO_CATS_URL}">{VIDEO_CATS_TITLE}</a> ({VIDEO_CATS_COUNT})</h3>
			<p class="small">{VIDEO_CATS_DESC}</p>
		<!-- END: VIDEO_CATS -->

		<!-- BEGIN: VIDEO_ROW -->
		<div class="item">
			<h3>{VIDEO_ROW_TITLE}</h3>
			<p class="small marginbottom10">{VIDEO_ROW_ADDED_STAMP|cot_date('date_medium', $this)}</p>

			<div>
				{VIDEO_ROW_PLAYER}
			</div>
		</div>
		<!-- END: VIDEO_ROW -->

		<p class="paging clear">
			{VIDEO_PAGEPREV}{VIDEO_PAGENAV}{VIDEO_PAGENEXT}
		</p>

	</div>

</div>

<!-- END: MAIN -->
