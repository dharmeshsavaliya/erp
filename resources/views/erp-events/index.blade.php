@extends('layouts.app')

@section('title', 'Erp Events')

@section("styles")
<link rel="stylesheet" type="text/css" href="/css/clndr.css">
@section('content')
<div class="row">
  <div class="col-lg-12 margin-tb">
      <h2 class="page-heading">Erp Events <a class="btn btn-secondary editor_create" href="javascript:;">+</a></h2>
  </div>
  <div class="col-md-12">
  	<div class="container">
        <div class="cal1"></div>
        <div class="cal2">
        <script type="text/template" id="template-calendar">
            <div class="clndr-controls">
                <div class="clndr-previous-button">&lsaquo;</div>
                <div class="month"><%= intervalStart.format('M/DD') + ' &mdash; ' + intervalEnd.format('M/DD') %></div>
                <div class="clndr-next-button">&rsaquo;</div>
            </div>
            <div class="clndr-grid">
                <div class="days-of-the-week">
                <% _.each(daysOfTheWeek, function(day) { %>
                    <div class="header-day"><%= day %></div>
                <% }); %>
                    <div class="days">
                    <% _.each(days, function(day) { %>
                        <div class="<%= day.classes %>"><%= day.day %></div>
                    <% }); %>
                    </div>
                </div>
            </div>
            <div class="clndr-today-button">Today</div>
        </script>
        </div>
        <div class="cal3">
        <script type="text/template" id="template-calendar-months">
            <div class="clndr-controls top">
                <div class="clndr-previous-button">&lsaquo;</div>
                <div class="clndr-next-button">&rsaquo;</div>
            </div>
            <div class="clearfix">
            <% _.each(months, function(cal) { %>
                <div class="cal">
                    <div class="clndr-controls">
                        <div class="month"><%= cal.month.format('MMMM') %></div>
                    </div>
                    <div class="clndr-grid">
                        <div class="days-of-the-week">
                        <% _.each(daysOfTheWeek, function(day) { %>
                            <div class="header-day"><%= day %></div>
                        <% }); %>
                            <div class="days">
                            <% _.each(cal.days, function(day) { %>
                                <div class="<%= day.classes %>"><%= day.day %></div>
                            <% }); %>
                            </div>
                        </div>
                    </div>
                </div>
            <% }); %>
            </div>
            <div class="clndr-today-button">Today</div>
        </script>
        </div>
    </div>
  </div>	
</div>
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script src="/js/clndr/clndr.min.js"></script>
@endsection
@endsection
