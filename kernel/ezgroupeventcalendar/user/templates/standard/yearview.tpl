<div id="calendarWrap" class="gcalYearView">

<div class="gcalYearToolbar">
  <span class="gcalSwitchBox" onmouseover="this.className='gcalSwitchBoxSelect'" onmouseout="this.className='gcalSwitchBox'"><a href="{www_dir}{index}/groupeventcalendar/dayview/{date_year}/{date_month}/{date_day}/" style="text-decoration:none;font-weight:normal;font-size:12px;">{intl-day}</a></span>
  <span class="gcalSwitchBox" onmouseover="this.className='gcalSwitchBoxSelect'" onmouseout="this.className='gcalSwitchBox'"><a href="{www_dir}{index}/groupeventcalendar/weekview/{date_year}/{date_month}/{date_day}/" style="text-decoration:none;font-weight:normal;font-size:12px;">{intl-week}</a></span>
  <span class="gcalSwitchBox" onmouseover="this.className='gcalSwitchBoxSelect'" onmouseout="this.className='gcalSwitchBox'"><a href="{www_dir}{index}/groupeventcalendar/monthview/{date_year}/{date_month}/" style="text-decoration:none;font-weight:normal;font-size:12px;">{intl-month}</a></span>
  <span class="gcalSwitchBox" onmouseover="this.className='gcalSwitchBoxSelect'" onmouseout="this.className='gcalSwitchBox'"><a href="{www_dir}{index}/groupeventcalendar/yearview/{date_year}/" style="text-decoration:none;font-weight:normal;font-size:12px;">{intl-year}</a></span>
</div>

<div class="gcalYearOuter">
<div id="gcalBigHeader" style="background: url('{www_dir}{index}/kernel/ezgroupeventcalendar/user/templates/standard/images/gcalShortTimeBg.png') repeat; text-align: center;">
  <a class="gcalMonthViewNext" href="{www_dir}{index}/groupeventcalendar/yearview/{prev_year_number}/">&lt;&lt;</a> &nbsp; &nbsp;
  <span style="font-size: 20px;">{year_number}</span> &nbsp; &nbsp;
  <a class="gcalMonthViewNext" href="{www_dir}{index}/groupeventcalendar/yearview/{next_year_number}/">&gt;&gt;</a>
</div>

<div class="gcalYearMonthGrid">
<!-- BEGIN month_tpl -->
<div class="gcalYearMonth">
<div class="gcalYearViewHeading" style="background: url('{www_dir}{index}/kernel/ezgroupeventcalendar/user/templates/standard/images/gcalSmallYearHeader.png') repeat;"><a class="gcalYearViewMonthName" href="{www_dir}{index}/groupeventcalendar/monthview/{year_number}/{month_number}/">{month_name}</a></div>
<div class="gcalYearMiniCal">
<!-- BEGIN week_tpl -->
<div class="gcalYearWeekRow">
<!-- BEGIN day_tpl -->
<div class="{td_class} gcalYearDayCell"><a class="{td_class}" href="{www_dir}{index}/groupeventcalendar/dayview/{year_number}/{month_number}/{day_number}/">{day_number}</a></div>
<!-- END day_tpl -->
<!-- BEGIN empty_day_tpl -->
<div class="gcalYearViewOff gcalYearDayCell">&nbsp;</div>
<!-- END empty_day_tpl -->
</div>
<!-- END week_tpl -->
</div>
</div>
<!-- END month_tpl -->
</div>

</div>
</div>
