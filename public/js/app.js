Date.prototype.getWeekNumber = function(){
    var d = new Date(Date.UTC(this.getFullYear(), this.getMonth(), this.getDate()));
    var dayNum = d.getUTCDay() || 7;
    d.setUTCDate(d.getUTCDate() + 4 - dayNum);
    var yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
    return Math.ceil((((d - yearStart) / 86400000) + 1)/7)
};

var app = angular.module('Lehrerkalender', ['ngRoute']);

app.controller('LessonsController', function($scope, $http) {
    $scope.hourNumbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
    $scope.days = [
        {name: 'Montag', number: 1},
        {name: 'Dienstag', number: 2},
        {name: 'Mittwoch', number: 3},
        {name: 'Donnerstag', number: 4},
        {name: 'Freitag', number: 5}
    ];

    $http.get('/api/classes/get').then(
        function(response) {
            $scope.classes = response.data;
        }
    );
    $http.get('/api/subjects/get').then(
        function(response) {
            $scope.subjects = response.data;
        }
    );
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

    $scope.reload();

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

    $scope.tileHidden = function(dayNumber, hourNumber) {
        if(_.isEmpty($scope.lessons) || _.isEmpty($scope.lessons[dayNumber])) {
            return false;
        }
        return _.some($scope.lessons[dayNumber], function(lesson) {
            return lesson.startNumber != lesson.endNumber
                && lesson.startNumber < hourNumber
                && lesson.endNumber >= hourNumber;
        });
    };

    $scope.getTileRowspan = function(dayNumber, hourNumber) {
        if(_.isEmpty($scope.lessons)
            || _.isEmpty($scope.lessons[dayNumber])
            || _.isEmpty($scope.lessons[dayNumber][hourNumber])) {
            return 1;
        }
        return $scope.lessons[dayNumber][hourNumber].startNumber - $scope.lessons[dayNumber][hourNumber].endNumber;
    };

    $scope.tileEmpty = function(dayNumber, hourNumber) {
        return _.isEmpty($scope.lessons) || _.isEmpty($scope.lessons[dayNumber]) || _.isEmpty($scope.lessons[dayNumber][hourNumber]);
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