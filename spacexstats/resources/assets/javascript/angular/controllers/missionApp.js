(function() {
    var app = angular.module('app', []);

    app.controller("missionController", ['$scope', 'missionService', function($scope, missionService) {
        // Scope the possible form data info
        $scope.data = {
            parts: laravel.parts,
            spacecraft: laravel.spacecraft,
            destinations: laravel.destinations,
            missionTypes: laravel.missionTypes,
            launchSites: laravel.launchSites,
            landingSites: laravel.landingSites,
            vehicles: laravel.vehicles,
            astronauts: laravel.astronauts,

            launchVideos: laravel.launchVideos ? laravel.launchVideos : null,
            missionPatches: laravel.missionPatches ? laravel.missionPatches : null,
            pressKits: laravel.pressKits ? laravel.pressKits : null,
            cargoManifests: laravel.cargoManifests ? laravel.cargoManifests : null,
            pressConferences: laravel.pressConferences ? laravel.pressConferences : null,
            featuredImages: laravel.featuredImages ? laravel.featuredImages: null,

            firstStageEngines: ['Merlin 1A', 'Merlin 1B', 'Merlin 1C', 'Merlin 1D'],
            upperStageEngines: ['Kestrel', 'Merlin 1C-Vac', 'Merlin 1D-Vac'],
            upperStageStatuses: ['Did not reach orbit', 'Decayed', 'Deorbited', 'Earth Orbit', 'Solar Orbit'],
            spacecraftTypes: ['Dragon 1', 'Dragon 2'],
            returnMethods: ['Splashdown', 'Landing', 'Did Not Return'],
            eventTypes: ['Wet Dress Rehearsal', 'Static Fire'],
            launchIlluminations: ['Day', 'Night', 'Twilight'],
            statuses: ['Upcoming', 'Complete', 'In Progress'],
            outcomes: ['Failure', 'Success']
        };

        $scope.filters = {
            parts: {
                type: ''
            }
        };

        $scope.mission = {
            data: laravel.mission,
            make: function() {
                missionService.make($scope.mission.data);
            }
        };
    }]);

    app.service("missionService", ["$http", "CSRF_TOKEN", function($http, CSRF_TOKEN) {
        this.make = function(mission) {
            if (mission.mission_id == null) {
                return $http.post('/missions/create', {
                    mission: mission,
                    _token: CSRF_TOKEN
                }).then(function (response) {
                    window.location = '/missions/' + response.data;
                });
            } else {
                return $http.patch('/missions/' + mission.slug + '/edit', {
                    mission: mission,
                    _token: CSRF_TOKEN
                });
            }
        };
    }]);
})();