<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<div id="calendarWrap">

<form method="post" action="{www_dir}{index}/groupeventcalendar/monthview/">
<div id="gcalDayViewSortBy">
  <div id="gcalDayViewSortByHeader"><img src="{www_dir}{index}/kernel/ezgroupeventcalendar/user/templates/standard/images/gcalX.png" style="margin-right:7px;" alt="close" onclick="document.getElementById('gcalDayViewSortBy').style.visibility = 'hidden';"/></div>
  <div class="gcalDayViewSortByBody">
    <span>{intl-group}:</span><br />
    <select class="gcalDayViewSelect" name="GetByGroupID">
      <option value="0">{intl-default}</option>
<!-- BEGIN group_item_tpl -->
      <option {group_is_selected} value="{group_id}">{group_name}</option>
<!-- END group_item_tpl -->
    </select>
    <br />
    <span>{intl-type}:</span><br />
    <select class="gcalDayViewSelect" name="GetByTypeID">
      <option value="0">{intl-default_type}</option>
<!-- BEGIN type_item_tpl -->
      <option {type_is_selected} value="{type_id}">{type_name}</option>
<!-- END type_item_tpl -->
    </select><br /><br />
    <input class="gcalDayViewButton" style="background: url('{www_dir}{index}/kernel/ezgroupeventcalendar/user/templates/standard/images/gcalButtonBg.png') repeat;" type="submit" name="GetByGroup" value="{intl-show}">
  </div>
</div>
</form>

<!-- BEGIN month_tpl -->
<div class="gcalMonthToolbar">
  <span class="gcalSwitchBox" onmouseover="this.className='gcalSwitchBoxSelect'" onmouseout="this.className='gcalSwitchBox'" onclick="document.getElementById('gcalDayViewSortBy').style.visibility = 'visible'; var posx = getMouse(event, 'x'); var posy = getMouse(event, 'y'); document.getElementById('gcalDayViewSortBy').style.left = posx + 'px'; document.getElementById('gcalDayViewSortBy').style.top = posy+ 'px';">Sort By...</span>
  <span class="gcalSwitchBox" onmouseover="this.className='gcalSwitchBoxSelect'" onmouseout="this.className='gcalSwitchBox'"><a href="{www_dir}{index}/groupeventcalendar/dayview/{date_year}/{date_month}/{date_day}/" style="text-decoration:none;font-weight:normal;font-size:12px;">{intl-day}</a></span>
  <span class="gcalSwitchBox" onmouseover="this.className='gcalSwitchBoxSelect'" onmouseout="this.className='gcalSwitchBox'"><a href="{www_dir}{index}/groupeventcalendar/weekview/{date_year}/{date_month}/{date_day}/" style="text-decoration:none;font-weight:normal;font-size:12px;">{intl-week}</a></span>
  <span class="gcalSwitchBox" onmouseover="this.className='gcalSwitchBoxSelect'" onmouseout="this.className='gcalSwitchBox'"><a href="{www_dir}{index}/groupeventcalendar/monthview/{date_year}/{date_month}/" style="text-decoration:none;font-weight:normal;font-size:12px;">{intl-month}</a></span>
  <span class="gcalSwitchBox" onmouseover="this.className='gcalSwitchBoxSelect'" onmouseout="this.className='gcalSwitchBox'"><a href="{www_dir}{index}/groupeventcalendar/yearview/{date_year}/" style="text-decoration:none;font-weight:normal;font-size:12px;">{intl-year}</a></span>
</div>

<div class="monthView">

<div id="gcalBigHeader" style="background: url('{www_dir}{index}/kernel/ezgroupeventcalendar/user/templates/standard/images/gcalShortTimeBg.png') repeat;">
  <a class="gcalMonthViewNext" href="{www_dir}{index}/groupeventcalendar/monthview/{prev_year_number}/{prev_month_number}/">&lt;&lt;</a> &nbsp; &nbsp;
  <span style="font-size: 20px;">{month_name} - {current_year_number}</span> &nbsp; &nbsp;
  <a class="gcalMonthViewNext" href="{www_dir}{index}/groupeventcalendar/monthview/{next_year_number}/{next_month_number}/">&gt;&gt;</a>
</div>

<div class="gcalMonthDayHeaders">
<!-- BEGIN week_day_tpl -->
<div class="gcalMonthDayHeader" style="background: url('{www_dir}{index}/kernel/ezgroupeventcalendar/user/templates/standard/images/gcalMonthViewHeaderBg.png') repeat;">{week_day_name}</div>
<!-- END week_day_tpl -->
</div>

<!-- BEGIN week_tpl -->
<div class="gcalMonthWeekRow">
<!-- BEGIN day_tpl -->
<div class="{td_class} gcalMonthDay">
  <div class="gcalMonthDayTop">
    <div>
<!-- BEGIN day_link_tpl --><a class="gcalBoxText" href="{www_dir}{index}/groupeventcalendar/dayview/{year_number}/{month_number_p}/{day_number}/{selected_group_id}/" title="Dayview">{day_number}</a>
<!-- END day_link_tpl -->
<!-- BEGIN day_no_link_tpl --><a class="gcalBoxText" href="" onmouseover="window.status='No Events in Day'; return true" onmouseout="window.status=''; return true" title="No Events in Day">{day_number}</a>
<!-- END day_no_link_tpl -->
    </div>
    <div>
<!-- BEGIN new_event_link_tpl --><a class="path" href="{www_dir}{index}/groupeventcalendar/eventedit/new/{year_number}/{month_number}/{day_number}/">+</a>
<!-- END new_event_link_tpl -->
<!-- BEGIN no_new_event_link_tpl -->&nbsp;
<!-- END no_new_event_link_tpl -->
    </div>
  </div>
<!-- BEGIN private_appointment_tpl -->
  <div class="gcalMonthAppt"><span class="small"><i>{appointment_group}</i> - <b>{intl-pvt_event}</b></span></div>
<!-- END private_appointment_tpl -->
<!-- BEGIN public_appointment_tpl -->
  <div class="gcalMonthAppt"><a class="gcalMonthViewNames" href="{www_dir}{index}/groupeventcalendar/eventview/{appointment_id}/" onmouseover="return overlib('<div class=\'olList\'>Name</div>{overlib_full_name}<div class=\'olList\'>Time</div> {event_start_time} - {event_stop_time}<div class=\'olList\'>Description </div>{overlib_description}');" onmouseout="return nd();">{appointment_name}</a></div>
<!-- END public_appointment_tpl -->
</div>
<!-- END day_tpl -->
</div>
<!-- END week_tpl -->

</div>
</div>
<!-- END month_tpl -->
<script language="javascript">
  document.querySelectorAll('.gcalMonthDayHeader').forEach(function(el) {
    el.setAttribute('data-short', el.textContent.trim().substring(0, 3));
  });
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
