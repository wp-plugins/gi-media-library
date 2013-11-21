<div id="giml_loader" style="height:20px">
</div>
<h1 id="groupheading">
	<span id="groupleftlabel">[+groupleftlabel+]<span id="grouprightlabel">[+grouprightlabel+]</span></span>
</h1>
<h5 id="subgroupheading">
	<span id="subgroupleftlabel">[+subgroupleftlabel+]<span id="subgrouprightlabel">[+subgrouprightlabel+]</span></span>
</h5>
<p>
<div id="subgroupdescription" style="[+subgroupdescriptionvisible+]" align="center">
 [+subgroupdescription+]
</div>
</p>
[+script+]
<div id="divsubgroupdownload" style="[+subgroupdownloadvisible+]" align="center">
  [+subgroupdownload+]
</div>
<div class="row" style="padding-left:0px">
 <div class="col">
  <span id="spansubgroupsearch" style="[+subgroupshowcombo+]">
   <label for="searchtype">[+playlistcombolabel+]</label>&nbsp;
	<select class="arabicName [+playlistcombocss+]" id="searchtype" 
                  dir="[+playlistcombodirection+]">
		[+playlistcomboitemssubgroup+]
   </select>&nbsp;
  </span>
 </div>
 <div class="col" style="float:right">
  <span id="spansubgroupfilter" style="[+subgroupshowfilter+]">
   <label for="filterby">Filter by:</label>&nbsp;
   <select id="filterby">
		[+subgroupfilteroptions+]
   </select>
  </span>
 </div>
</div>
<div style="clear:both"></div>
<div id="giml_playlistcomboitemdescription" style="[+playlistcomboitemdescriptionvisible+]" align="center">
 [+playlistcomboitemdescription+]
</div><br/>
<div id="giml_playlistcomboitemdownload" style="[+playlistcomboitemdownloadvisible+]" align="center">
 [+playlistcomboitemdownload+]
</div>
<div id="mediaList2">
	<div class="Head"><div>&nbsp;</div></div>
	<div id="titles"><div class="titles">
	  <table id="playList" summary=""
	  cellpadding="0"
	  cellspacing="0" class="[+playlisttablecss+]">
		<thead>
		  <tr id="playlistHeader">
[+tableheader+]
		  </tr>
		</thead>
		<tbody id="playlistBody">
[+tablerows+]
		</tbody>
	  </table>
	</div></div>
	<div class="foot">
	  &nbsp;

	</div>
</div>