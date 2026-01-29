@extends('layouts.app')
@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- Enhanced Page Header -->
        <div
            class="mb-8 bg-gradient-to-r from-accent/10 to-accent-hover/10 rounded-xl p-6 border-l-4 border-accent shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-primary-text flex items-center">
                        <svg class="w-8 h-8 mr-3 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        Calendar Management
                    </h1>
                    <p class="text-secondary-text mt-2 ml-11"> Manage studio availability, business hours, and view
                        scheduled bookings. </p>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Main Calendar -->
            <div class="w-full lg:w-3/4 bg-card-bg rounded-xl shadow-lg p-6 border border-border-color text-primary-text">

                <div class="flex flex-wrap justify-between items-center mb-6 gap-4 pb-4 border-b border-border-color">

                    <div class="flex items-center gap-3">

                        <div>
                            <select id="calendar-view-type"
                                class="form-select py-2.5 px-4 cursor-pointer bg-background text-primary-text border-border-color rounded-lg hover:border-accent focus:border-accent focus:ring-2 focus:ring-accent/20 transition-all shadow-sm">
                                <option value="dayGridMonth">📅 Month View</option>
                                <option value="timeGridWeek">📆 Week View</option>
                                <option value="timeGridDay">📋 Day View</option>
                                <option value="listWeek">📝 List View</option>
                            </select>
                        </div>

                        <button id="calendar-today"
                            class="text-sm bg-accent hover:bg-accent-hover text-white font-medium px-4 py-2.5 rounded-lg cursor-pointer transition-all shadow-sm hover:shadow-md">
                            Today
                        </button>

                    </div>

                    <div class="flex gap-3">

                        <button id="manage-slots-btn"
                            class="bg-gradient-to-r from-accent to-accent-hover hover:from-accent-hover hover:to-accent text-white font-medium px-4 py-2.5 rounded-lg flex items-center shadow-sm hover:shadow-md transition-all"><svg
                                class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg><span class="hidden sm:inline">Manage Slots</span></button>

                        <button id="manage-business-hours-btn"
                            class="bg-gradient-to-r from-info to-info/90 hover:from-info/90 hover:to-info text-white font-medium px-4 py-2.5 rounded-lg flex items-center shadow-sm hover:shadow-md transition-all"><svg
                                class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg><span class="hidden sm:inline">Business Hours</span></button>

                    </div>

                </div>
                <!-- Enhanced Calendar Legend -->
                <div
                    class="flex items-center gap-3 text-sm mb-6 flex-wrap bg-gradient-to-r from-background to-background/50 p-4 rounded-lg border border-border-color shadow-sm">
                    <div class="font-bold text-primary-text mr-2 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                        Legend:
                    </div>

                    <div class="flex items-center bg-green-50 px-3 py-2 rounded-lg border border-green-200 hover:shadow-md transition-all">
                        <span class="w-3.5 h-3.5 inline-block bg-green-500 rounded-full mr-2 shadow-sm border-2 border-green-600"></span>
                        <span class="text-green-700 font-semibold text-xs">Confirmed</span>
                    </div>

                    <div class="flex items-center bg-yellow-50 px-3 py-2 rounded-lg border border-yellow-200 hover:shadow-md transition-all">
                        <span class="w-3.5 h-3.5 inline-block bg-yellow-500 rounded-full mr-2 shadow-sm border-2 border-yellow-600"></span>
                        <span class="text-yellow-700 font-semibold text-xs">Pending</span>
                    </div>

                    <div class="flex items-center bg-red-50 px-3 py-2 rounded-lg border border-red-200 hover:shadow-md transition-all">
                        <span class="w-3.5 h-3.5 inline-block bg-red-500 rounded-full mr-2 shadow-sm border-2 border-red-600"></span>
                        <span class="text-red-700 font-semibold text-xs">Cancelled</span>
                    </div>

                    <div class="flex items-center bg-slate-50 px-3 py-2 rounded-lg border border-slate-200 hover:shadow-md transition-all">
                        <span class="w-3.5 h-3.5 inline-block bg-slate-400 rounded-full mr-2 shadow-sm border-2 border-slate-500"></span>
                        <span class="text-slate-700 font-semibold text-xs">Unavailable</span>
                    </div>

                    <div class="flex items-center ml-auto bg-accent/10 px-3 py-2 rounded-lg border border-accent/30 hover:bg-accent/15 transition-all">
                        <svg class="w-4 h-4 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-xs text-accent font-semibold">Click a date to mark unavailable</span>
                    </div>

                </div>
                <!-- Calendar View -->
                <div id="calendar-view" class="min-h-[600px] calendar-theme">

                </div>

            </div>
            <!-- Sidebar -->
            <div class="w-full lg:w-1/4 space-y-6">
                <!-- Unavailable Dates Manager -->
                <div
                    class="bg-card-bg rounded-xl shadow-lg overflow-hidden border border-border-color hover:shadow-xl transition-shadow text-primary-text">

                    <div class="p-4 border-b border-border-color bg-gradient-to-r from-accent to-accent-hover">

                        <h2 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Mark Dates Unavailable
                        </h2>

                    </div>

                    <div class="p-6 bg-gradient-to-b from-background/50 to-card-bg">

                        <form id="unavailable-date-form">

                            <div class="mb-4">
                                <label for="start_date"
                                    class="block text-sm font-semibold mb-2 text-primary-text flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-accent" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    Start Date
                                </label>
                                <input type="date" id="start_date" name="start_date"
                                    class="form-input w-full bg-background text-primary-text border-border-color rounded-lg focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all shadow-sm"
                                    required>

                            </div>

                            <div class="mb-4">
                                <label for="end_date"
                                    class="block text-sm font-semibold mb-2 text-primary-text flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-accent" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    End Date
                                </label>
                                <input type="date" id="end_date" name="end_date"
                                    class="form-input w-full bg-background text-primary-text border-border-color rounded-lg focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all shadow-sm"
                                    required>

                            </div>

                            <div class="mb-4">
                                <label for="reason"
                                    class="block text-sm font-semibold mb-2 text-primary-text flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-accent" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                                        </path>
                                    </svg>
                                    Reason (Optional)
                                </label>
                                <textarea id="reason" name="reason"
                                    class="form-input w-full bg-background text-primary-text border-border-color rounded-lg focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all shadow-sm resize-none"
                                    rows="3" placeholder="e.g., Studio maintenance, holidays, special event..."></textarea>

                            </div>

                            <button type="submit"
                                class="bg-gradient-to-r from-accent to-accent-hover hover:from-accent-hover hover:to-accent text-white font-semibold w-full py-3 rounded-lg flex items-center justify-center shadow-md hover:shadow-lg transition-all"><svg
                                    class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg> Mark as Unavailable </button>

                        </form>

                    </div>

                </div>
                <!-- Unavailable Dates List -->
                <div
                    class="bg-card-bg rounded-xl shadow-lg overflow-hidden border border-border-color hover:shadow-xl transition-shadow text-primary-text">

                    <div
                        class="p-4 border-b border-border-color bg-gradient-to-r from-danger/90 to-danger flex justify-between items-center">

                        <h2 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                            Unavailable Dates
                        </h2>

                        <div class="text-xs font-semibold bg-white/20 px-3 py-1 rounded-full text-white"
                            id="unavailable-count">
                            0 dates
                        </div>

                    </div>

                    <div class="p-4 bg-gradient-to-b from-background/50 to-card-bg">

                        <div id="unavailable-dates-list" class="space-y-3 max-h-80 overflow-y-auto pr-1">

                            <div class="text-center py-4">

                                <div
                                    class="animate-spin w-8 h-8 border-4 border-accent border-t-transparent rounded-full mx-auto mb-2">

                                </div>

                                <div class="text-sm text-secondary-text">
                                    Loading...
                                </div>

                            </div>

                        </div>

                    </div>

                </div>
                <!-- Studio Operating Hours Summary -->
                <div
                    class="bg-card-bg rounded-xl shadow-lg overflow-hidden border border-border-color hover:shadow-xl transition-shadow">

                    <div class="p-4 border-b border-border-color bg-gradient-to-r from-info to-info/90">

                        <h2 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Studio Hours
                        </h2>

                    </div>

                    <div class="p-4 bg-gradient-to-b from-background/50 to-card-bg">

                        <div id="business-hours-summary" class="space-y-2 text-sm">

                            <div class="text-center py-4">

                                <div
                                    class="animate-spin w-8 h-8 border-4 border-accent border-t-transparent rounded-full mx-auto mb-2">

                                </div>

                                <div class="text-sm text-secondary-text">
                                    Loading...
                                </div>

                            </div>

                        </div>

                        <button id="studio-hours-btn"
                            class="mt-4 bg-gradient-to-r from-info to-info/90 hover:from-info/90 hover:to-info text-white font-semibold w-full py-3 rounded-lg flex items-center justify-center shadow-md hover:shadow-lg transition-all"><svg
                                class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                </path>
                            </svg> Edit Studio Hours </button>

                    </div>

                </div>

            </div>

        </div>

    </div>
    <!-- Business Hours Modal -->
    <div id="business-hours-modal"
        class="modal-container hidden fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">

        <div
            class="modal-content bg-card-bg w-full max-w-lg rounded-xl shadow-2xl transform transition-all opacity-0 -translate-y-4 max-h-[90vh] flex flex-col border border-border-color text-primary-text">
            <div
                class="p-5 border-b border-border-color flex items-center justify-between shrink-0 bg-gradient-to-r from-info to-info/90">

                <h2 class="text-xl font-bold text-white flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Business Hours
                </h2>

                <button class="modal-close p-2 rounded-lg hover:bg-white/20 text-white transition-all"><svg
                        class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg></button>

            </div>

            <div class="p-6 overflow-y-auto custom-scrollbar bg-gradient-to-b from-background/30 to-card-bg">

                <div class="bg-accent/10 border-l-4 border-accent p-4 rounded-lg mb-4">
                    <p class="text-sm text-primary-text flex items-center">
                        <svg class="w-5 h-5 mr-2 text-accent flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Set your studio's operating hours for each day. These hours determine available booking slots.
                    </p>
                </div>

                <form id="business-hours-form">

                    <div id="business-hours-container" class="space-y-6">
                        <!-- Business hours will be populated here -->
                        <div class="text-center py-4">

                            <div
                                class="animate-spin w-8 h-8 border-4 border-accent border-t-transparent rounded-full mx-auto mb-2">

                            </div>

                            <div class="text-sm">
                                Loading...
                            </div>

                        </div>

                    </div>

                    <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-border-color">

                        <button type="button"
                            class="px-6 py-2.5 border-2 border-border-color bg-background hover:bg-card-bg text-secondary-text hover:text-primary-text rounded-lg transition-all font-medium modal-close">
                            Cancel </button>

                        <button type="submit" id="save-hours-btn"
                            class="bg-gradient-to-r from-success to-success/90 hover:from-success/90 hover:to-success text-white font-semibold py-2.5 px-8 rounded-lg flex items-center shadow-md hover:shadow-lg transition-all"><svg
                                class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg> Save Changes </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
    <!-- Booking Slots Modal -->
    <div id="booking-slots-modal"
        class="modal-container hidden fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">

        <div
            class="modal-content bg-card-bg w-full max-w-xl rounded-xl shadow-2xl transform transition-all opacity-0 -translate-y-4 max-h-[90vh] flex flex-col border border-border-color text-primary-text">

            <div
                class="p-5 border-b border-border-color flex items-center justify-between shrink-0 bg-gradient-to-r from-accent to-accent-hover">

                <h2 class="text-xl font-bold text-white flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    Manage Booking Slots
                </h2>

                <button class="modal-close p-2 rounded-lg hover:bg-white/20 text-white transition-all"><svg
                        class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg></button>

            </div>

            <div class="p-6 overflow-y-auto custom-scrollbar bg-gradient-to-b from-background/30 to-card-bg">

                <div class="flex flex-col sm:flex-row gap-4 mb-6 p-4 bg-accent/5 rounded-lg border border-accent/20">

                    <div class="flex-1">
                        <label for="slot-date"
                            class="block text-sm font-semibold mb-2 text-primary-text flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-accent" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            Select Date
                        </label>
                        <input type="date" id="slot-date"
                            class="form-input w-full bg-background text-primary-text border-border-color rounded-lg focus:ring-2 focus:ring-accent/20 focus:border-accent shadow-sm">

                    </div>

                    <div class="flex-1">

                        <p class="block text-sm font-semibold mb-2 text-primary-text flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-accent" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Status
                        </p>

                        <div id="date-status"
                            class="text-sm py-2.5 px-4 bg-background/80 text-primary-text rounded-lg border border-border-color shadow-sm font-medium">
                            Select a date to view status
                        </div>

                    </div>

                </div>

                <!-- Slots Legend -->
                <div class="mb-4 p-3 bg-background/50 rounded-lg border border-border-color">
                    <p class="text-xs font-semibold mb-2 text-primary-text">Legend:</p>
                    <div class="flex flex-wrap gap-3 text-xs">
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-green-500"></span>
                            <span class="text-secondary-text">Available</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-red-500"></span>
                            <span class="text-secondary-text">Fully Booked</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                            <span class="text-secondary-text">Limited Slots</span>
                        </div>
                    </div>
                </div>

                <div id="slots-container"
                    class="min-h-[200px] max-h-[400px] overflow-y-auto border-2 border-border-color rounded-lg custom-scrollbar bg-background/30">

                    <div class="text-center py-12">

                        <p class="text-secondary-text">Select a date to view available slots</p>

                    </div>

                </div>

                <div
                    class="mt-6 flex items-center justify-between text-sm bg-info/10 p-4 rounded-lg border-l-4 border-info">

                    <div class="text-primary-text flex items-center">
                        <svg class="w-5 h-5 mr-2 text-info flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Slots are automatically generated based on business hours.</span>
                    </div>

                    <div>

                        <button id="refresh-slots-btn"
                            class="bg-accent hover:bg-accent-hover text-white text-xs font-medium py-2 px-4 rounded-lg flex items-center shadow-sm hover:shadow-md transition-all"><svg
                                class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg> Refresh </button>

                    </div>

                </div>

            </div>

        </div>

    </div>
    <!-- Event Details Modal -->
    <div id="event-details-modal"
        class="modal-container hidden fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">

        <div
            class="modal-content bg-card-bg w-full max-w-md rounded-xl shadow-2xl transform transition-all opacity-0 -translate-y-4 border border-border-color text-primary-text">

            <div
                class="p-5 border-b border-border-color flex items-center justify-between bg-gradient-to-r from-success to-success/90">

                <h2 class="text-xl font-bold text-white flex items-center" id="event-title">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                    Event Details
                </h2>

                <button class="modal-close p-2 rounded-lg hover:bg-white/20 text-white transition-all"><svg
                        class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg></button>
            </div>

            <div class="p-6 bg-gradient-to-b from-background/20 to-card-bg">

                <div id="event-details-content"
                    class="space-y-3 bg-background/30 p-4 rounded-lg border border-border-color">
                    <!-- Event details will be populated here -->
                </div>

                <div class="mt-6 flex justify-between gap-3 pt-4 border-t border-border-color">

                    <button
                        class="modal-close bg-background hover:bg-background/80 text-primary-text font-medium py-2.5 px-5 rounded-lg shadow-sm hover:shadow-md transition-all">Close</button>
                    <a href="#" id="event-view-btn"
                        class="bg-gradient-to-r from-success to-success/90 hover:from-success/90 hover:to-success text-white font-medium py-2.5 px-5 rounded-lg flex items-center shadow-sm hover:shadow-md transition-all"><svg
                            class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                        </svg>View Details</a>
                </div>

            </div>

        </div>

    </div>
    <!-- Date Availability Modal -->
    <div id="date-availability-modal"
        class="modal-container hidden fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">

        <div
            class="modal-content bg-card-bg w-full max-w-md rounded-xl shadow-2xl transform transition-all opacity-0 -translate-y-4 border border-border-color text-primary-text">

            <div
                class="p-5 border-b border-border-color flex items-center justify-between bg-gradient-to-r from-info to-info/90">

                <h2 class="text-xl font-bold text-white flex items-center" id="selected-date-title">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    Set Date Availability
                </h2>

                <button class="modal-close p-2 rounded-lg hover:bg-white/20 text-white transition-all"><svg
                        class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg></button>

            </div>

            <div class="p-6 bg-gradient-to-b from-background/20 to-card-bg">

                <form id="date-availability-form">

                    <div class="mb-6 p-4 bg-info/10 rounded-lg border-l-4 border-info">

                        <p id="date-availability-info" class="mb-2 text-primary-text flex items-center font-medium">
                            <svg class="w-5 h-5 mr-2 text-info flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            You are about to change availability for:
                        </p>

                        <p id="selected-date-display" class="text-lg font-semibold text-primary-text ml-7"></p>

                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-3 text-primary-text flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-accent" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Current Status:
                        </label>
                        <div id="current-date-status"
                            class="px-4 py-3 rounded-lg text-center font-semibold bg-background/50 border-2 border-border-color shadow-sm text-primary-text">
                            Checking...
                        </div>

                    </div>

                    <div id="make-unavailable-section" class="mb-6">
                        <label for="availability-reason"
                            class="block text-sm font-semibold mb-3 text-primary-text flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-accent" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            Reason for unavailability (optional):
                        </label>
                        <textarea id="availability-reason"
                            class="form-input w-full bg-background text-primary-text border-border-color rounded-lg focus:ring-2 focus:ring-accent/20 focus:border-accent shadow-sm"
                            rows="3" placeholder="Studio maintenance, holidays, etc."></textarea>

                    </div>

                    <div id="already-unavailable-section" class="mb-6 hidden">

                        <div class="p-4 rounded-lg bg-danger/10 border-l-4 border-danger flex items-start">
                            <svg class="w-5 h-5 mr-2 text-danger flex-shrink-0 mt-0.5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                            <div class="flex-1">
                                <p id="unavailable-reason" class="font-semibold text-danger mb-1"></p>

                                <p class="text-sm text-primary-text">Would you like to make this date available again?</p>
                            </div>

                        </div>

                    </div>

                    <div class="flex justify-between gap-3 pt-4 border-t border-border-color">

                        <button type="button"
                            class="modal-close bg-background hover:bg-background/80 text-primary-text font-medium py-2.5 px-5 rounded-lg shadow-sm hover:shadow-md transition-all">Cancel</button>

                        <button type="button" id="date-availability-action"
                            class="bg-gradient-to-r from-accent to-accent-hover hover:from-accent/90 hover:to-accent text-white font-medium py-2.5 px-5 rounded-lg flex items-center justify-center shadow-sm hover:shadow-md transition-all"><svg
                                class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg><span id="availability-action-text">Mark as Unavailable</span></button>

                    </div>

                </form>

            </div>

        </div>

    </div>

    <!-- Include Confirmation Modal -->
    @include('modals.delete-unavailable-date-confirmation')
@endsection
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <link href="{{ asset('css/calendar.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="{{ asset('js/calendar.js') }}"></script>
@endpush
