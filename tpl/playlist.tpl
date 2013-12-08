<div id="giml_content">
	<div id="giml_loader"></div>
	<h3 id="groupheading">
		<span id="groupleftlabel">[+groupleftlabel+]<span id="grouprightlabel">[+grouprightlabel+]</span></span>
	</h3>
	<h5 id="subgroupheading">
		<span id="subgroupleftlabel">[+subgroupleftlabel+]<span id="subgrouprightlabel">[+subgrouprightlabel+]</span></span>
	</h5>
	<div id="subgroupdescription" style="[+subgroupdescriptionvisible+]" align="center">
	 [+subgroupdescription+]
	</div>
	[+script+]
	<div id="subgroupdownload" style="[+subgroupdownloadvisible+]" align="center">
	  [+subgroupdownload+]
	</div>
	<div id="giml_filter">
	 <div class="col">
	  <span id="spansubgroupsearch" style="[+subgroupshowcombo+]">
	   <label for="searchtype">[+playlistcombolabel+]</label>&nbsp;
		<select class="[+playlistcombocss+]" id="searchtype" 
					  dir="[+playlistcombodirection+]">
			[+playlistcomboitemssubgroup+]
	   </select>&nbsp;
	  </span>
	 </div>
	 <div class="col-right">
	  <span id="spansubgroupfilter" style="[+subgroupshowfilter+]">
	   <label for="filterby">Filter by:</label>&nbsp;
	   <select id="filterby">
			[+subgroupfilteroptions+]
	   </select>
	  </span>
	 </div>
	 <div class="clear"></div>
	</div>
	<div id="giml_playlistcomboitemdescription" style="[+playlistcomboitemdescriptionvisible+]" align="center">
	 [+playlistcomboitemdescription+]
	</div>
	<div id="giml_playlistcomboitemdownload" style="[+playlistcomboitemdownloadvisible+]" align="center">
	 [+playlistcomboitemdownload+]
	</div>
	<div id="giml_playlist">
		  <table id="playList" summary="" cellpadding="0" cellspacing="0" class="[+playlisttablecss+]">
			<thead>
			  <tr id="playlistHeader">
	[+tableheader+]
			  </tr>
			</thead>
			<tbody id="playlistBody">
	[+tablerows+]
			</tbody>
		  </table>
	</div>
</div>