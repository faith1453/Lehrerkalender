Date.prototype.getWeekNumber = function(){
    var d = new Date(Date.UTC(this.getFullYear(), this.getMonth(), this.getDate()));
    var dayNum = d.getUTCDay() || 7;
    d.setUTCDate(d.getUTCDate() + 4 - dayNum);
    var yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
    return Math.ceil((((d - yearStart) / 86400000) + 1)/7)
};

var app = angular.module('Lehrerkalender', ['ngRoute']);

app.controller('LessonsController', function($scope, $http) {
    /*$http.get('api/lessons/get').then(
        function(result) {
            $scope.lessons = result.data;
        }
    );*/
    var date = new Date();
    $scope.week = date.getWeekNumber();
    $scope.year = date.getFullYear();

    $scope.reload = function() {
        $http.get('/api/lessons/get/' + $scope.year + '/' + $scope.week).then(
            function(response) {
                $scope.lessons = response.data;
            }
        );
    };

    $scope.previousWeek = function() {
        $scope.week -= 1;
        if($scope.week < 1) {
            $scope.week = 52;
            $scope.year -= 1;
        }
        $scope.reload();
    };

    $scope.nextWeek = function() {
        $scope.week += 1;
        if($scope.week > 52) {
            $scope.week = 1;
            $scope.year += 1;
        }
        $scope.reload();
    };
});

app.config(function($routeProvider) {
    $routeProvider
        .when("/", {
            templateUrl: 'templates/main.html'
        })
        .when("/lessons", {
            templateUrl: 'templates/lessons.html',
            controller: 'LessonsController'
        })
});