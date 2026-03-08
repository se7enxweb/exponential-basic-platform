<div id="calendarWrap">
<script>
 function objChangeVisiblity(objName){
  document.getElementById(objName).style.visibility = 'hidden';
 }
</script>
<div id="overDiv" style="position: absolute; visibility: hidden; z-index: 1000;"></div>

<!-- Sort-by filter popup (position: absolute, draggable) -->
<form method="post" action="{www_dir}{index}/groupeventcalendar/dayview/">
<div id="gcalDayViewSortBy">
 <div id="gcalDayViewSortByHeader">
  <a href="javascript:objChangeVisiblity('gcalDayViewSortBy')" style="text-decoration: none;">
   <img src="{www_dir}{index}/kernel/ezgroupeventcalendar/user/templates/standard/images/gcalX.png" style="z-index: 1001; margin-right:7px; margin-top: 2px;" alt="close" border="0" />
  </a>
 </div>
 <div class="gcalDayViewSortByBody">
  <span>{intl-group}:</span><br />
  <select class="gcalDayViewSelect" name="GetByGroupID">
   <option value="0">{intl-default}</option>
   <!-- BEGIN group_item_tpl -->
   <option {group_is_selected} value="{group_id}">{group_name}</option>
   <!-- END group_item_tpl -->
  </select><br />
  <span>{intl-type}:</span><br />
  <select class="gcalDayViewSelect" name="GetByTypeID">
   <option value="0">{intl-default_type}</option>
   <!-- BEGIN type_item_tpl -->
   <option {type_is_selected} value="{type_id}">{type_name}</option>
   <!-- END type_item_tpl -->
  </select><br /><br />
  <input class="gcalDayViewButton" style="background: url('{www_dir}{index}/kernel/ezgroupeventcalendar/user/templates/standard/images/gcalButtonBg.png') repeat;" type="submit" Name="GetByGroup" value="{intl-show}">
 </div>
</div>
</form>

<!-- Mini-calendar popup (position: absolute, draggable) -->
<div id="gcalDayViewMonthTable">
 <div id="gcalDayViewMonthTableHeader">
  <a href="javascript:objChangeVisiblity('gcalDayViewMonthTable')" style="text-decoration: none;">
   <img src="{www_dir}{index}/kernel/ezgroupeventcalendar/user/templates/standard/images/gcalX.png" style="height: 12px; z-index: 1001; margin-right:7px;" alt="close" border="0" />
  </a>
 </div>
 <div class="gcalMiniCalSubHeader" style="background: no-repeat url('{www_dir}{index}/kernel/ezgroupeventcalendar/user/templates/standard/images/gcalDayViewMonthTableSubHeader.png'); text-align: center;">
  <a class="gcalDayViewMonthTableHeader" href="{www_dir}{index}/groupeventcalendar/dayview/{pm_year_number}/{pm_month_number}/{pm_day_number}/{group_print_id}/">&lt;&lt;&nbsp;</a>
  <a class="gcalDayViewMonthTableHeader" href="{www_dir}{index}/groupeventcalendar/monthview/{year_number}/{month_number}/{group_print_id}/">{month_name}</a>
  <a class="gcalDayViewMonthTableHeader" href="{www_dir}{index}/groupeventcalendar/dayview/{nm_year_number}/{nm_month_number}/{nm_day_number}/{group_print_id}/">&nbsp;&gt;&gt;</a>
 </div>
 <!-- BEGIN week_tpl -->
 <div class="gcalMiniCalWeek">
  <!-- BEGIN day_tpl -->
  <div class="gcalDayViewMonthTableDay">
   <a class="gcalDayViewMonthTableDay" href="{www_dir}{index}/groupeventcalendar/dayview/{year_number}/{month_number}/{day_number}/{group_print_id}/">{day_number}</a>
  </div>
  <!-- END day_tpl -->
  <!-- BEGIN empty_day_tpl -->
  <div class="gcalDayViewMonthTableEmpty">&nbsp;</div>
  <!-- END empty_day_tpl -->
 </div>
 <!-- END week_tpl -->
</div>

<!-- Main form: toolbar + day header + time grid (shared for new/delete actions) -->
<form method="post" action="{www_dir}{index}/groupeventcalendar/eventedit/edit/">

<!-- Toolbar: view switches, new/delete buttons, sort/calendar toggles -->
<div class="gcalDayViewToolbar">
 <!-- BEGIN valid_editor_tpl -->
 <input class="gcalSwitchBox" onmouseover="this.className='gcalSwitchBoxSelect'" onmouseout="this.className='gcalSwitchBox'" type="submit" name="GoNew" value="{intl-new_event}">&nbsp;
 <input class="gcalSwitchBox" onmouseover="this.className='gcalSwitchBoxSelect'" onmouseout="this.className='gcalSwitchBox'" type="submit" name="DeleteEvents" value="{intl-delete_events}">
 <!-- END valid_editor_tpl -->
 <span class="gcalSwitchBox" onmouseover="this.className='gcalSwitchBoxSelect'"
  onmouseout="this.className='gcalSwitchBox'"
  onclick="document.getElementById('gcalDayViewSortBy').style.visibility = 'visible';
  var posx = getMouse(event, 'x');
  var posy = getMouse(event, 'y');
  document.getElementById('gcalDayViewSortBy').style.left = posx + 'px';
  document.getElementById('gcalDayViewSortBy').style.top = posy+ 'px';">
  Sort By
 </span>
 <span class="gcalSwitchBox" onmouseover="this.className='gcalSwitchBoxSelect'"
  onmouseout="this.className='gcalSwitchBox'"
  onclick="document.getElementById('gcalDayViewMonthTable').style.visibility = 'visible';
  var posx = getMouse(event, 'x');
  var posy = getMouse(event, 'y');
  document.getElementById('gcalDayViewMonthTable').style.left = posx + 'px';
  document.getElementById('gcalDayViewMonthTable').style.top = posy+ 'px';">
  Show Calendar
 </span>
 <span class="gcalSwitchBox" onmouseover="this.className='gcalSwitchBoxSelect'" onmouseout="this.className='gcalSwitchBox'">
  <a href="{www_dir}{index}/groupeventcalendar/weekview/{the_year}/{the_month}/{the_day}/{group_print_id}" style="text-decoration:none; font-weight:normal;font-size:12px;">{intl-week}</a>
 </span>
 <span class="gcalSwitchBox" onmouseover="this.className='gcalSwitchBoxSelect'" onmouseout="this.className='gcalSwitchBox'">
  <a href="{www_dir}{index}/groupeventcalendar/monthview/{the_year}/{the_month}/{group_print_id}/" style="text-decoration:none; font-weight:normal;font-size:12px;">{intl-month}</a>
 </span>
 <span class="gcalSwitchBox" onmouseover="this.className='gcalSwitchBoxSelect'" onmouseout="this.className='gcalSwitchBox'">
  <a href="{www_dir}{index}/groupeventcalendar/yearview/{the_year}/{group_print_id}/" style="text-decoration:none; font-weight:normal;font-size:12px;">{intl-year}</a>
 </span>
 <span class="gcalSwitchBox" onmouseover="this.className='gcalSwitchBoxSelect'" onmouseout="this.className='gcalSwitchBox'">
  <a href="{www_dir}{index}/groupeventcalendar/dayview/{year_cur}/{month_cur}/{day_cur}/{group_print_id}/" style="text-decoration:none; font-weight:normal;font-size:12px;">{intl-today}</a>
 </span>
</div>

<!-- Day header: date title + day nav + all-day events -->
<div class="gcalDayViewHeaderSection" style="border: 1px solid gray;">
<!-- BEGIN day_view_long_date_header_tpl -->
<div id="gcalBigHeader" style="background: url('{www_dir}{index}/kernel/ezgroupeventcalendar/user/templates/standard/images/gcalShortTimeBg.png') repeat;">
 <span class="gcalBigHeader">
  <a style="font-size: large; text-decoration: none; color: #000;" href="{www_dir}{index}/groupeventcalendar/monthview/{year_number}/{month_number}/{group_print_id}/">{long_date}</a>
 </span>
</div>
<div class="gcalDayViewNavBar">
 <div class="gcalDayViewTopBar gcalDayViewNavPrev">
  <a class="gcalSmallLink" href="{www_dir}{index}/groupeventcalendar/dayview/{pd_year_number}/{pd_month_number}/{pd_day_number}/{group_print_id}/"> &lt;&lt; </a>
 </div>
 <!-- BEGIN day_links_tpl -->
 <div class="{class_name}"
  onmouseover="this.className='gcalDayViewTopBarSelect'"
  onmouseout="this.className='{class_name}'"
  onclick="location.href = '{www_dir}{index}/groupeventcalendar/dayview/{top_year_number}/{top_month_number}/{top_day_number}/{group_print_id}/'">{day_name}</div>
 <!-- END day_links_tpl -->
 <div class="gcalDayViewTopBar gcalDayViewNavNext">
  <a class="gcalSmallLink" href="{www_dir}{index}/groupeventcalendar/dayview/{nd_year_number}/{nd_month_number}/{nd_day_number}/{group_print_id}/"> &gt;&gt; </a>
 </div>
</div>
<!-- BEGIN all_day_event_tpl -->
<div class="gcalAllDayRow">
 <div class="gcalDayViewTopBar gcalAllDayLabel">All Day</div>
 <div class="gcalAllDayEventBody"
  onclick="location.href = '{www_dir}{index}/groupeventcalendar/eventview/{all_day_id}/'"
  style="cursor: pointer; background: url('{www_dir}{index}/kernel/ezgroupeventcalendar/user/templates/standard/images/gcalAllDayEvent.png') repeat;">
  <a class="gcalAllDay" href="{www_dir}{index}/groupeventcalendar/eventview/{all_day_id}/"
   onmouseover="return overlib('<div class=\'olWrapAllDay\'><div class=\'olListAllDay\'>Name</div>{all_day_overlib_name}<div class=\'olListAllDay\'>Time</div> {all_day_start} - {all_day_stop}<div class=\'olListAllDay\'>Description </div>{all_day_desc}</div>');"
   onmouseout="return nd();">{all_day_name}</a>
 </div>
 <!-- BEGIN all_day_delete_check_tpl -->
 <div class="gcalAllDayAction" style="background: url('{www_dir}{index}/kernel/ezgroupeventcalendar/user/templates/standard/images/gcalAllDayEvent.png') repeat;">
  <a href="{www_dir}{index}/groupeventcalendar/eventedit/edit/{all_day_id}/">
   <img name="ezcal{event_id}-red" border="0" src="{www_dir}{index}/design/base/images/icons/redigermini.gif" width="12" height="12" align="top" alt="Edit" />
  </a>
 </div>
 <div class="gcalAllDayAction" style="background: url('{www_dir}{index}/kernel/ezgroupeventcalendar/user/templates/standard/images/gcalAllDayEvent.png') repeat;">
  <input type="checkbox" name="eventArrayID[]" value={all_day_id}>
 </div>
 <!-- END all_day_delete_check_tpl -->
 <div class="gcalDayViewTopBar gcalAllDayLabel">All Day</div>
</div>
<!-- END all_day_event_tpl -->
<!-- END day_view_long_date_header_tpl -->
</div>

<!-- Time grid -->
<div class="gcalTimeGrid gcalBorder" style="background: url('{www_dir}{index}/kernel/ezgroupeventcalendar/user/templates/standard/images/gcalDayViewBg.png') repeat;">
 <!-- Left: time-of-day labels -->
 <div class="gcalTimeLabels">
  <!-- BEGIN time_display_tpl -->
  <!-- BEGIN new_event_link_tpl -->
  <div class="{td_class} gcalTimeSlot" style="text-align: center; border: 1px solid gray; border-right: 2px solid gray; background: url('{www_dir}{index}/kernel/ezgroupeventcalendar/user/templates/standard/images/gcalShortTimeBg.png') repeat;">
   <a class="path" style="font-size: 10px;" href="{www_dir}{index}/groupeventcalendar/eventedit/new/{year_number}/{month_number}/{day_number}/{display_start_time}/{group_print_id}/">{short_time}</a>
  </div>
  <!-- END new_event_link_tpl -->
  <!-- BEGIN no_new_event_link_tpl -->
  <div class="{td_class} gcalTimeSlot" style="text-align: center; border: 1px solid gray; border-right: 2px solid gray; font-size: 10px; background: url('{www_dir}{index}/kernel/ezgroupeventcalendar/user/templates/standard/images/gcalShortTimeBg.png') repeat;">{short_time}</div>
  <!-- END no_new_event_link_tpl -->
  <!-- END time_display_tpl -->
 </div>
 <!-- Right: event columns (one gcalTimeRow per time slot, matching left labels) -->
 <div class="gcalEventsArea">
  <!-- BEGIN time_table_tpl -->
  <div class="gcalTimeRow">
  <!-- BEGIN fifteen_event_tpl -->
  <div class="{td_class} gcalFifteenEvent" style="overflow: hidden; background-color: #cc6666; background: url('{www_dir}{index}/kernel/ezgroupeventcalendar/user/templates/standard/images/gcalEventTransBg.png') repeat;">
   <div class="gcalEventInner">
    <div class="gcalEventTopBar gcalEventName">&nbsp;<a class='gcalDayEventText' href="{www_dir}{index}/groupeventcalendar/eventview/{event_id}/" onmouseover="return overlib('<div class=\'olList\'>Name</div>{overlib_event_name}<div class=\'olList\'>Time</div> {event_start} - {event_stop}<div class=\'olList\'>Description </div>{overlib_event_description}');" onmouseout="return nd();">{event_name}&nbsp;&nbsp;</a></div>
    <!-- BEGIN fifteen_delete_check_tpl -->
    <div class="gcalEventTopBar gcalEventActions">
     <input type="checkbox" name="eventArrayID[]" value={event_id} style="width: 10px; height: 10px;">
     <a href="{www_dir}{index}/groupeventcalendar/eventedit/edit/{event_id}/">
      <img name="ezcal{event_id}-red" border="0" src="/design/admin/images/redigermini.gif" width="12" height="12" align="top" alt="Edit" style="padding-top: 2px;" />
     </a>
    </div>
    <div class="gcalEventTopBar">&nbsp;</div>
    <!-- END fifteen_delete_check_tpl -->
    <!-- BEGIN fifteen_no_delete_check_tpl -->
    <div class="gcalEventTopBar">&nbsp;</div>
    <div class="gcalEventTopBar">&nbsp;</div>
    <!-- END fifteen_no_delete_check_tpl -->
   </div>
  </div>
  <!-- END fifteen_event_tpl -->
  <!-- BEGIN public_event_tpl -->
  <div class="{td_class} gcalPublicEvent" style="height: calc({rowspan_value} * 15px); border: 1px solid black; overflow: hidden; background-color: #cc6666; background: url('{www_dir}{index}/kernel/ezgroupeventcalendar/user/templates/standard/images/gcalEventTransBg.png') repeat;">
   <div class="gcalEventInner">
    <div class="gcalEventTopBar gcalEventName" style="overflow: hidden; white-space: nowrap;">
     <a class='gcalDayEventText' href="{www_dir}{index}/groupeventcalendar/eventview/{event_id}/" onmouseover="return overlib('<div class=\'olList\'>Name</div>{overlib_event_name}<div class=\'olList\'>Time</div> {event_start} - {event_stop}<div class=\'olList\'>Description </div>{overlib_event_description}');" onmouseout="return nd();">&nbsp;{event_name}&nbsp;</a>
    </div>
    <!-- BEGIN delete_check_tpl -->
    <div class="gcalEventTopBar gcalEventActions">
     <a href="{www_dir}{index}/groupeventcalendar/eventedit/edit/{event_id}/">
      <img name="ezcal{event_id}-red" border="0" src="{www_dir}{index}/design/base/images/icons/redigermini.gif" width="12" height="12" alt="Edit" />
     </a>
    </div>
    <div class="gcalEventTopBar gcalEventActions">
     <input type="checkbox" name="eventArrayID[]" value={event_id}>
    </div>
    <!-- END delete_check_tpl -->
    <!-- BEGIN no_delete_check_tpl -->
    <div class="gcalEventTopBar">&nbsp;</div>
    <div class="gcalEventTopBar">&nbsp;</div>
    <!-- END no_delete_check_tpl -->
   </div>
   <div class="gcalDayEventText" style="font-weight: bold; overflow: hidden; height: {event_div_height}px;">
    &nbsp;{event_description}
   </div>
   <div class="gcalEventFooter">&laquo; {event_name} &bull; {event_start}&ndash;{event_stop}</div>
  </div>
  <!-- END public_event_tpl -->
  <!-- BEGIN private_event_tpl -->
  <div class="{td_class} gcalPrivateEvent" style="height: calc({rowspan_value} * 15px);">
   <b><i>{event_groupName} - {intl-private_event}</i></b>
   <div class="gcalEventFooter">&laquo; {event_start}&ndash;{event_stop}</div>
  </div>
  <!-- END private_event_tpl -->
  <!-- BEGIN no_event_tpl -->
  <div class="gcalNoEvent"></div>
  <!-- END no_event_tpl -->
  </div><!-- end gcalTimeRow -->
  <!-- END time_table_tpl -->
 </div>
</div>

</form>
</div>
<script language="javascript">
  var mthDiv = document.getElementById("gcalDayViewMonthTableHeader");
  var mtDiv   = document.getElementById("gcalDayViewMonthTable");
  Drag.init(mthDiv, mtDiv);
  Drag.init(document.getElementById("gcalDayViewSortBy"));
divX=0
divY=0
function getMouse(fnEvent, type)
{
    if(typeof(fnEvent.clientX)=='number' && typeof(fnEvent.clientY)=='number')
{
divX = fnEvent.clientX
divY = fnEvent.clientY
}
else if(typeof(fnEvent.x)=='number' && typeof(fnEvent.y)=='number')
{
divX = fnEvent.x
divY = fnEvent.y
}
else
{
divX = 500
divY = 500
}
  if (type == 'x')
   return divX;
  else
   return divY;
}
</script>

<script>
(function () {
  /* Give the events column a stacking context so raised cards
     don't escape underneath the toolbar or sidebar. */
  var area = document.querySelector('.gcalEventsArea');
  if (area) area.style.isolation = 'isolate';

  function closeAll() {
    document.querySelectorAll('.gcalEventActive').forEach(function (el) {
      el.classList.remove('gcalEventActive');
      el.style.overflow = 'hidden';
      el.style.zIndex   = '';
    });
  }

  document.querySelectorAll('.gcalPublicEvent, .gcalPrivateEvent').forEach(function (ev) {
    ev.addEventListener('click', function (e) {
      /* Pass through links, checkboxes, images */
      if (e.target.closest('a, input, button, img')) return;
      e.stopPropagation();

      var wasActive = ev.classList.contains('gcalEventActive');
      closeAll();

      if (!wasActive) {
        ev.classList.add('gcalEventActive');
        ev.style.overflow = 'visible';
        ev.style.zIndex   = '500';
      }
    });
  });

  /* Click outside → dismiss */
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.gcalPublicEvent, .gcalPrivateEvent')) closeAll();
  });

  /* Escape key → dismiss */
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeAll();
  });
}());
</script>

<script>
/* Shorten day names in the nav bar on narrow / mobile screens */
(function () {
  var SHORT = {
    'Monday':'Mon', 'Tuesday':'Tue', 'Wednesday':'Wed',
    'Thursday':'Thu', 'Friday':'Fri', 'Saturday':'Sat', 'Sunday':'Sun'
  };
  var LONG = {};
  for (var k in SHORT) LONG[SHORT[k]] = k;

  function getDayDivs() {
    /* Day-link divs have onclick= on the div itself; arrow divs wrap an <a>.
       Use the onclick attribute to select only the day-name cells. */
    return Array.prototype.slice.call(
      document.querySelectorAll('.gcalDayViewNavBar div[onclick]')
    );
  }

  function applyShort() {
    getDayDivs().forEach(function (el) {
      var t = el.textContent.trim();
      if (SHORT[t]) el.textContent = SHORT[t];
    });
  }

  function applyLong() {
    getDayDivs().forEach(function (el) {
      var t = el.textContent.trim();
      if (LONG[t]) el.textContent = LONG[t];
    });
  }

  function check(mq) {
    if (mq.matches) applyShort(); else applyLong();
  }

  var mq = window.matchMedia('(max-width: 640px)');
  check(mq);
  /* Use the modern addEventListener when available, fall back to addListener */
  if (mq.addEventListener) {
    mq.addEventListener('change', check);
  } else {
    mq.addListener(check);
  }
}());
</script>
