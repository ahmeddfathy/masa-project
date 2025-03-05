@extends('layouts.admin')

@section('styles')
    <link rel="stylesheet" href="/assets/css/admin/bookings.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.css' rel='stylesheet' />
<style>
    .fc-event {
        cursor: pointer;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .fc-event-pending { background-color: #ffc107; border-color: #ffc107; }
    .fc-event-confirmed { background-color: #28a745; border-color: #28a745; }
    .fc-event-completed { background-color: #17a2b8; border-color: #17a2b8; }
    .fc-event-cancelled { background-color: #dc3545; border-color: #dc3545; }

    @media (max-width: 768px) {
        .card-body {
            padding: 0.5rem;
        }

        #calendar {
            font-size: 14px;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">تقويم الحجوزات</h3>
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل الحجز</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="bookingDetails"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/ar.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'ar',
        direction: 'rtl',
        initialView: window.innerWidth < 768 ? 'dayGridDay' : 'dayGridMonth',
        headerToolbar: {
            right: 'prev,next today',
            center: 'title',
            left: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        views: {
            dayGridMonth: {
                titleFormat: { year: 'numeric', month: 'long' }
            },
            timeGridWeek: {
                titleFormat: { year: 'numeric', month: 'long', day: '2-digit' }
            },
            timeGridDay: {
                titleFormat: { year: 'numeric', month: 'long', day: '2-digit' }
            }
        },
        windowResize: function(view) {
            if (window.innerWidth < 768) {
                calendar.changeView('dayGridDay');
            } else {
                calendar.changeView('dayGridMonth');
            }
        },
        eventDidMount: function(info) {
            info.el.title = info.event.title;
        },
        events: {!! json_encode($bookings->map(function($booking) {
            return [
                'id' => $booking->uuid,
                'title' => $booking->user->name . ' - ' . $booking->service->name,
                'start' => $booking->session_date->format('Y-m-d') . 'T' . $booking->session_time->format('H:i:s'),
                'className' => 'fc-event-' . $booking->status,
                'extendedProps' => [
                    'bookingNumber' => $booking->booking_number,
                    'status' => $booking->status,
                    'package' => $booking->package->name,
                    'total' => $booking->total_amount
                ]
            ];
        })) !!},
        eventClick: function(info) {
            var event = info.event;
            var html = `
                <div class="booking-details">
                    <p><strong>رقم الحجز:</strong> ${event.extendedProps.bookingNumber}</p>
                    <p><strong>العميل:</strong> ${event.title}</p>
                    <p><strong>الباقة:</strong> ${event.extendedProps.package}</p>
                    <p><strong>الحالة:</strong> ${getStatusInArabic(event.extendedProps.status)}</p>
                    <p><strong>المبلغ:</strong> ${event.extendedProps.total} درهم</p>
                    <div class="text-center mt-3">
                        <a href="/admin/bookings/${event.id}" class="btn btn-primary">عرض التفاصيل</a>
                    </div>
                </div>
            `;
            document.getElementById('bookingDetails').innerHTML = html;
            var modal = new bootstrap.Modal(document.getElementById('bookingModal'));
            modal.show();
        }
    });
    calendar.render();
});

function getStatusInArabic(status) {
    const statuses = {
        'pending': 'قيد الانتظار',
        'confirmed': 'مؤكد',
        'completed': 'مكتمل',
        'cancelled': 'ملغي'
    };
    return statuses[status] || status;
}
</script>
@endsection
