<!-- Export Report Modal -->
<div id="exportReportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[9999] flex items-center justify-center p-2 sm:p-4 transition-opacity duration-300 opacity-0" onclick="closeExportModal()">
    <div class="bg-card-bg rounded-xl shadow-2xl max-w-lg w-full max-h-[95vh] flex flex-col transform transition-all duration-300 scale-95 modal-content" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-accent to-accent-hover text-white px-4 sm:px-6 py-4 sm:py-5 rounded-t-xl flex-shrink-0">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 flex-1">
                    <div class="bg-white/20 rounded-lg p-1.5 sm:p-2 flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-lg sm:text-xl font-bold truncate">Export Report</h3>
                        <p class="text-xs text-white/80 mt-0.5 hidden sm:block">Download your data in various formats</p>
                    </div>
                </div>
                <button type="button" onclick="closeExportModal()" class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all duration-200 flex-shrink-0 touch-manipulation">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="p-4 sm:p-6 overflow-y-auto flex-1">
            <!-- Info Banner -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-3 sm:p-4 mb-4 sm:mb-6 rounded-r-lg">
                <div class="flex items-start gap-2 sm:gap-3">
                    <svg class="w-5 h-5 text-blue-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-blue-800">Quick Export</p>
                        <p class="text-xs text-blue-700 mt-1">Select your preferred format and date range below. Leave dates blank for current month data.</p>
                    </div>
                </div>
            </div>

            <!-- Export Type Selection (for dashboard) -->
            <div id="exportTypeSection" class="mb-4 sm:mb-6 hidden">
                <label class="block text-sm font-semibold text-primary-text mb-2 sm:mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-accent" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                    </svg>
                    Report Type
                </label>
                <div class="grid grid-cols-1 gap-3">
                    <label class="relative flex items-center p-3 sm:p-4 border-2 border-border-color rounded-xl cursor-pointer hover:border-accent hover:bg-accent/5 transition-all duration-200 group touch-manipulation">
                        <input type="radio" name="exportType" value="dashboard" class="sr-only peer" checked>
                        <div class="flex items-center space-x-3 w-full relative z-0">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-accent/10 to-accent/20 flex items-center justify-center group-hover:from-accent/20 group-hover:to-accent/30 transition-all duration-200 flex-shrink-0">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-primary-text text-sm sm:text-base">Dashboard Overview</div>
                                <div class="text-xs text-secondary-text mt-0.5 line-clamp-1">Complete summary with all metrics and KPIs</div>
                            </div>
                        </div>
                        <div class="absolute right-3 sm:right-4 top-1/2 transform -translate-y-1/2 w-5 h-5 sm:w-6 sm:h-6 border-2 border-gray-300 rounded-full peer-checked:border-accent peer-checked:border-[6px] transition-all duration-200 z-10 flex-shrink-0"></div>
                    </label>
                </div>
            </div>

            <!-- Date Range Selection -->
            <div class="mb-4 sm:mb-6">
                <label class="block text-sm font-semibold text-primary-text mb-2 sm:mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-accent" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                    Date Range
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div>
                        <label class="block text-xs font-medium text-secondary-text mb-2">Start Date</label>
                        <div class="relative">
                            <input type="date" id="exportStartDate" class="w-full px-3 py-3 sm:py-2.5 pl-9 sm:pl-10 border-2 border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent text-sm transition-all duration-200 hover:border-accent/50 touch-manipulation">
                            <svg class="w-4 h-4 text-secondary-text absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-secondary-text mb-2">End Date</label>
                        <div class="relative">
                            <input type="date" id="exportEndDate" class="w-full px-3 py-3 sm:py-2.5 pl-9 sm:pl-10 border-2 border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent text-sm transition-all duration-200 hover:border-accent/50 touch-manipulation">
                            <svg class="w-4 h-4 text-secondary-text absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="flex items-start mt-3 p-2 sm:p-2.5 bg-gray-50 rounded-lg gap-2">
                    <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-xs text-gray-600 flex-1">Leave blank to export current month data</p>
                </div>
            </div>

            <!-- Format Selection -->
            <div class="mb-4 sm:mb-6">
                <label class="block text-sm font-semibold text-primary-text mb-2 sm:mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-accent" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                    </svg>
                    Export Format
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- PDF Option -->
                    <label for="format-pdf" class="relative flex flex-col items-center p-4 sm:p-4 border-2 border-border-color rounded-xl cursor-pointer hover:border-red-400 hover:bg-red-50 hover:shadow-lg transition-all duration-200 group touch-manipulation min-h-[120px] sm:min-h-0">
                        <input type="radio" id="format-pdf" name="exportFormat" value="pdf" class="absolute opacity-0 pointer-events-none peer" checked>
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-red-50 flex items-center justify-center mb-2 sm:mb-3 group-hover:bg-red-100 group-hover:scale-110 transition-all duration-200 peer-checked:bg-red-100 peer-checked:scale-110 relative z-0">
                            <svg class="w-7 h-7 sm:w-8 sm:h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="text-sm sm:text-base font-bold text-primary-text relative z-0">PDF</span>
                        <span class="text-xs text-secondary-text mt-1 text-center relative z-0">Print-ready format</span>
                        <div class="absolute top-2 sm:top-3 right-2 sm:right-3 w-5 h-5 sm:w-6 sm:h-6 border-2 border-gray-300 rounded-full peer-checked:border-red-500 peer-checked:bg-red-500 transition-all duration-200 flex items-center justify-center z-10">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </label>

                    <!-- Excel Option -->
                    <label for="format-excel" class="relative flex flex-col items-center p-4 sm:p-4 border-2 border-border-color rounded-xl cursor-pointer hover:border-green-400 hover:bg-green-50 hover:shadow-lg transition-all duration-200 group touch-manipulation min-h-[120px] sm:min-h-0">
                        <input type="radio" id="format-excel" name="exportFormat" value="excel" class="absolute opacity-0 pointer-events-none peer">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-green-50 flex items-center justify-center mb-2 sm:mb-3 group-hover:bg-green-100 group-hover:scale-110 transition-all duration-200 peer-checked:bg-green-100 peer-checked:scale-110 relative z-0">
                            <svg class="w-7 h-7 sm:w-8 sm:h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 6a1 1 0 011-1h12a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="text-sm sm:text-base font-bold text-primary-text relative z-0">Excel</span>
                        <span class="text-xs text-secondary-text mt-1 text-center relative z-0">Editable spreadsheet</span>
                        <div class="absolute top-2 sm:top-3 right-2 sm:right-3 w-5 h-5 sm:w-6 sm:h-6 border-2 border-gray-300 rounded-full peer-checked:border-green-500 peer-checked:bg-green-500 transition-all duration-200 flex items-center justify-center z-10">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </label>

                    <!-- CSV Option -->
                    <label for="format-csv" class="relative flex flex-col items-center p-4 sm:p-4 border-2 border-border-color rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 hover:shadow-lg transition-all duration-200 group touch-manipulation min-h-[120px] sm:min-h-0">
                        <input type="radio" id="format-csv" name="exportFormat" value="csv" class="absolute opacity-0 pointer-events-none peer">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-blue-50 flex items-center justify-center mb-2 sm:mb-3 group-hover:bg-blue-100 group-hover:scale-110 transition-all duration-200 peer-checked:bg-blue-100 peer-checked:scale-110 relative z-0">
                            <svg class="w-7 h-7 sm:w-8 sm:h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8 2a1 1 0 000 2h2a1 1 0 100-2H8z"></path>
                                <path d="M3 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6h-4.586l1.293-1.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L10.414 13H15v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"></path>
                            </svg>
                        </div>
                        <span class="text-sm sm:text-base font-bold text-primary-text relative z-0">CSV</span>
                        <span class="text-xs text-secondary-text mt-1 text-center relative z-0">Raw data file</span>
                        <div class="absolute top-2 sm:top-3 right-2 sm:right-3 w-5 h-5 sm:w-6 sm:h-6 border-2 border-gray-300 rounded-full peer-checked:border-blue-500 peer-checked:bg-blue-500 transition-all duration-200 flex items-center justify-center z-10">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Export Progress -->
            <div id="exportProgress" class="hidden mb-4">
                <div class="flex items-center justify-center space-x-3 text-accent bg-accent/10 p-3 sm:p-4 rounded-xl border-2 border-accent/20">
                    <svg class="animate-spin h-5 w-5 sm:h-6 sm:w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold">Generating report...</p>
                        <p class="text-xs text-secondary-text">Please wait while we prepare your file</p>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <div id="exportError" class="hidden mb-4 p-3 sm:p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm">
                <div class="flex items-start space-x-2 sm:space-x-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-red-800 font-semibold">Export Failed</p>
                        <p id="exportErrorMessage" class="text-xs text-red-700 mt-1 break-words"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="bg-gray-50 px-4 sm:px-6 py-3 sm:py-4 rounded-b-xl flex flex-col-reverse sm:flex-row justify-between items-stretch sm:items-center border-t border-gray-200 gap-3 sm:gap-0 flex-shrink-0">
            <div class="text-xs text-secondary-text text-center sm:text-left">
                <span class="inline-flex items-center justify-center sm:justify-start">
                    <svg class="w-4 h-4 mr-1 text-accent flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="truncate">Download starts automatically</span>
                </span>
            </div>
            <div class="flex flex-col-reverse sm:flex-row gap-2 sm:gap-3 w-full sm:w-auto">
                <button type="button" onclick="closeExportModal()" class="w-full sm:w-auto px-4 sm:px-5 py-3 sm:py-2.5 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 hover:border-gray-400 transition-all duration-200 font-medium text-sm touch-manipulation active:scale-95">
                    Cancel
                </button>
                <button type="button" onclick="confirmExport()" id="exportButton" class="w-full sm:w-auto px-5 sm:px-6 py-3 sm:py-2.5 bg-gradient-to-r from-accent to-accent-hover hover:from-accent-hover hover:to-accent text-white rounded-lg transition-all duration-200 font-semibold text-sm shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 active:translate-y-0 active:scale-95 flex items-center justify-center space-x-2 touch-manipulation">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Export Now</span>
                </button>
            </div>
        </div>
    </div>
</div>
