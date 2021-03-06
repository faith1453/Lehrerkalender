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
    $scope.lessonSelected = false;

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

    $scope.openLesson = function(dayNumber, hourNumber) {
        if($scope.tileEmpty(dayNumber, hourNumber)) {
            if(_.isEmpty($scope.lessons)) {
                $scope.lessons = [];
            }
            if(_.isEmpty($scope.lessons[dayNumber])) {
                $scope.lessons[dayNumber] = [];
            }
            if(_.isEmpty($scope.lessons[dayNumber][hourNumber])) {
                var newLesson = {
                    topic: '',
                    startNumber: hourNumber,
                    endNumber: hourNumber,
                    class_id: null,
                    subject_id: null
                };
                $scope.lessons[dayNumber][hourNumber] = newLesson;
                $scope.activeLesson = newLesson;
            }
        } else {
            $scope.activeLesson = $scope.lessons[dayNumber][hourNumber];
        }
        $scope.activeLessonDayNumber = dayNumber;
        $scope.lessonSelected = true;
    };

    $scope.expandTile = function(dayNumber, hourNumber) {
        var lesson = $scope.lessons[dayNumber][hourNumber];
        var futureEndNumber = lesson.endNumber + 1;
        if(!_.isEmpty($scope.lessons[dayNumber][futureEndNumber])) {
            return;
        }
        lesson.endNumber += 1;
    };

    $scope.saveActiveLesson = function() {
        if(_.isEmpty($scope.activeLesson)) {
            return;
        }
        $http.post('/api/lessons/save', {
            lesson: $scope.activeLesson,
            year: $scope.year,
            week: $scope.week,
            day: $scope.activeLessonDayNumber
        }).then(
            function(response) {
                $scope.activeLesson.id = response.data;
                console.log('Success');
            },
            function(response) {
                console.log('Failure');
            }
        )
    };

    $scope.collapseTile = function(dayNumber, hourNumber) {
        var lesson = $scope.lessons[dayNumber][hourNumber];
        lesson.endNumber -= 1;
        if(lesson.endNumber < lesson.startNumber) {
            lesson.endNumber = lesson.startNumber;
        }
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

    $scope.tileHidden = function(dayNumber, hourNumber) {
        if(_.isEmpty($scope.lessons) || _.isEmpty($scope.lessons[dayNumber])) {
            return false;
        }
        return _.some($scope.lessons[dayNumber], function(lesson) {
            if(typeof lesson === 'undefined') {
                return false;
            }
            return lesson.startNumber !== lesson.endNumber
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
        return 1 + $scope.lessons[dayNumber][hourNumber].endNumber - $scope.lessons[dayNumber][hourNumber].startNumber;
    };

    $scope.tileEmpty = function(dayNumber, hourNumber) {
        return _.isEmpty($scope.lessons) || _.isEmpty($scope.lessons[dayNumber]) || _.isEmpty($scope.lessons[dayNumber][hourNumber]);
    };
});

app.controller('GradesController', function($scope, $http) {
    $http.get('/api/classes/get').then(
        function(response) {
            $scope.classes = response.data;
        }
    );
    $scope.reload = function() {
        $http.get('/api/lessons/dates/' + $scope.class.id).then(
            function(response) {
                $scope.lessons = response.data;
            }
        );
        $http.get('/api/students/get/' + $scope.class.id).then(
            function(response) {
                $scope.students = response.data;
            }
        );
        $http.get('/api/grades/get/' + $scope.class.id).then(
            function(response) {
                $scope.grades = response.data;
            }
        )
    };
    $scope.saveGrades = function() {
        $http.post('/api/grades/save', {grades: $scope.grades});
    };

    $scope.gradeExists = function(lessonId, studentId) {
        return (!_.isEmpty($scope.grades)
            && !_.isEmpty($scope.grades[lessonId])
            && !_.isEmpty($scope.grades[lessonId][studentId]));
    };

    $scope.getStudentAverage = function(studentId) {
        var total = 0;
        var number = 0;
        _.each($scope.grades, function(grades) {
            if(!_.isEmpty(grades[studentId]) && !_.isEmpty(grades[studentId][0].grade)) {
                total +=  parseFloat(grades[studentId][0].grade);
                number++;
            }
        });
        if(number === 0) {
            return 0;
        }
        return (total / number);
    };
});

app.controller('ExamsController', function($scope, $http) {
    $scope.isClassSelected = false;

    $http.get('/api/classes/get').then(
        function(response) {
            $scope.classes = response.data;
        }
    );

    $scope.reload = function() {
        $http.get('/api/exams/get/' + $scope.class.id).then(
            function(response) {
                $scope.exams = response.data;
                _.each($scope.exams, function(exam) {
                    _.each(exam.student_exams, function(studentExam) {
                        _.each(studentExam.tasks, function(task) {
                            task.pivot.points = parseFloat(task.pivot.points);
                        })
                    })
                })
            }
        );
        $scope.isClassSelected = true;
    };

    $scope.createNewExam = function() {
        $http.get('/api/exams/new/' + $scope.class.id).then(function() {
            $scope.reload();
        })
    };

    $scope.saveExam = function() {
        $http.post('/api/exams/save', {exam: $scope.exam});
    };

    $scope.getStudentPoints = function(studentExam) {
        return _.reduce(studentExam.tasks, function(memo, task) {
            return memo + task.pivot.points;
        }, 0);
    };

    $scope.getStudentPercentage = function(studentExam) {
        return ($scope.getStudentPoints(studentExam) / $scope.exam.max_points * 100);
    };

    $scope.getStudentTask = function(studentExam, origTask) {
        return _.find(studentExam.tasks, function(task) {
            if(typeof origTask.id !== 'undefined') {
                return origTask.id === task.id;
            }
            return origTask.tmp_id === task.tmp_id;
        })
    };

    $scope.tmpTaskId = 0;
    $scope.newTask = function() {
        var newTask = {
            tmp_id: $scope.tmpTaskId,
            exam_id: $scope.exam.id,
            name: 'Neue Aufgabe'
        };
        $scope.exam.tasks.push(newTask);
        _.each($scope.exam.student_exams, function(studentExam) {
            newTask = {
                tmp_id: $scope.tmpTaskId,
                exam_id: $scope.exam.id,
                name: '',
                pivot: {
                    points: 0
                }
            };
            console.log(studentExam);
            studentExam.tasks.push(newTask);
        });
        $scope.tmpTaskId++;
    }
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
        .when("/grades", {
            templateUrl: 'templates/grades.html',
            controller: 'GradesController'
        })
        .when("/exams", {
            templateUrl: 'templates/exams.html',
            controller: 'ExamsController'
        })
});