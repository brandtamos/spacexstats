(function() {
    var app = angular.module('app');

    app.directive('launchDateValidity', [function() {
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function($scope, element, attributes, ngModelCtrl) {

                var subs = ['Early', 'Mid', 'Late'].join('|');
                var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October',
                'November', 'December'].join('|');
                var quarters = ['Q1', 'Q2', 'Q3', 'Q4'].join('|');
                var halfs = ['H1', 'H2'].join('|');


                ngModelCtrl.$validators.launchDateValidity = function(value) {
                    if (angular.isDefined(value) && value !== null) {
                        // Check day and date specificities
                        if (moment(value, 'YYYY-MM-DD H:mm:ss', true).isValid()) {
                            return true;
                        }

                        // Submonth specificities
                        var submonthRegex = new RegExp('^(' + subs + ') (' + months + ') \\d{4}$');
                        // Month specificities
                        var monthRegex = new RegExp('^(' + months + ') \\d{4}$');
                        // Quarter specifities
                        var quarterRegex = new RegExp('^(' + quarters + ') \\d{4}$');
                        // Subyear specificities
                        var subyearRegex = new RegExp('^(' + subs + ') \\d{4}$');
                        // Half specificities
                        var halfRegex = new RegExp('^(' + halfs + ') \\d{4}$');
                        // Year specificities
                        var yearRegex = new RegExp('^\\d{4}$');

                        var regexes = [submonthRegex, monthRegex, quarterRegex, subyearRegex, halfRegex, yearRegex];

                        regexes.every(function(regex) {
                            if (regex.test(value)) {
                                return true;
                            }
                        });
                    }
                    return false;
                };
            }
        }
    }]);
})();