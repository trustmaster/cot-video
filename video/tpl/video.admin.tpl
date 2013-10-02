<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.themes_dir}/admin/{PHP.cfg.admintheme}/warnings.tpl"}

<div class="block">
	<!-- IF {PHP.video_has_categories} -->
	<form name="filter" method="get" action="{VIDEO_ADMIN_FILTER_ACTION}">
		<input type="hidden" name="m" value="video">
		<div>
			<strong>{PHP.L.Show}:</strong> &nbsp; {VIDEO_ADMIN_FILTER_CAT} &nbsp; <button type="submit" class="button">{PHP.L.Filter}</button>
		</div>
	</form>
	<!-- ENDIF -->
	<h3>{PHP.L.vid_all}</h3>
	<table class="cells">
		<tr>
			<!-- IF {PHP.video_has_categories} -->
			<td class="coltop">
				{PHP.L.Category}
			</td>
			<!-- ENDIF -->
			<td class="coltop">
				{PHP.L.vid_priority}
			</td>
			<td class="coltop">
				{PHP.L.Title}
			</td>
			<td class="coltop">
				{PHP.L.vid_source}
			</td>
			<td class="coltop">
				{PHP.L.Code}
			</td>
			<td class="coltop">
				{PHP.L.Update}
			</td>
			<td class="coltop">
				{PHP.L.Delete}
			</td>
		</tr>
		<!-- BEGIN: VIDEO_ADMIN_ROW -->
		<tr>
			<form action="{VIDEO_ADMIN_ROW_UPDATE_URL}" method="post">
			<!-- IF {PHP.video_has_categories} -->
			<td>{VIDEO_ADMIN_ROW_CAT}</td>
			<!-- ENDIF -->
			<td>
				{VIDEO_ADMIN_ROW_ORDER}
			</td>
			<td>
				{VIDEO_ADMIN_ROW_TITLE}
			</td>
			<td>
				{VIDEO_ADMIN_ROW_SOURCE}
			</td>
			<td>
				{VIDEO_ADMIN_ROW_CODE}
			</td>
			<td>
				<button type="submit">{PHP.L.Update}</button>
			</td>
			<td>
				<a class="button" href="{VIDEO_ADMIN_ROW_DELETE_URL}" class="button">{PHP.L.Delete}</a>
			</td>
			</form>
		</tr>
		<!-- END: VIDEO_ADMIN_ROW -->
	</table>

	<p class="paging">{VIDEO_ADMIN_PAGEPREV} {VIDEO_ADMIN_PAGENAV} {VIDEO_ADMIN_PAGENEXT}</p>
</div>


<div class="block">
	<h3>{PHP.L.vid_add}</h3>
	<form action="{VIDEO_ADMIN_ADD_URL}" method="post">
	<table class="cells">
		<tr>
		<!-- IF {PHP.video_has_categories} -->
			<td class="coltop">
				{PHP.L.Category}
			</td>
		<!-- ENDIF -->
			<td class="coltop">
				{PHP.L.Title}
			</td>
			<td class="coltop">
				{PHP.L.vid_source}
			</td>
			<td class="coltop">
				{PHP.L.Code}
			</td>
			<td class="coltop"></td>
		</tr>
		<tr>
			<!-- IF {PHP.video_has_categories} -->
			<td>{VIDEO_ADMIN_ADD_CAT}</td>
			<!-- ENDIF -->
			<td>
				{VIDEO_ADMIN_ADD_TITLE}
			</td>
			<td>
				{VIDEO_ADMIN_ADD_SOURCE}
			</td>
			<td>
				{VIDEO_ADMIN_ADD_CODE}
			</td>
			<td><button type="submit">{PHP.L.Add}</button></td>
		</tr>
	</table>
	</form>
</div>

<!-- END: MAIN -->
