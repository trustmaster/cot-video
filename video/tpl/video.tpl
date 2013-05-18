<!-- BEGIN: MAIN -->

<div class="col first">
	<div class="block">
		<h2>{PHP.L.vid_all}</h2>

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
