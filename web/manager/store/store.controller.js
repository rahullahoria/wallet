(function () {
    'use strict';

    angular
        .module('app')
        .controller('StoreController', StoreController);

    StoreController.$inject = ['UserService', '$cookieStore', 'CandidateService', '$routeParams', 'FlashService','$location'];
    function StoreController(UserService, $cookieStore, CandidateService,  $routeParams, FlashService,$location) {
        var vm = this;

        vm.user = null;
        vm.inUser = null;
        vm.allUsers = [];
        vm.deleteUser = deleteUser;
        vm.loadUser = loadUser;

        vm.champs = 0;
        vm.good = 0;
        vm.improve = 0;
        vm.bad = 0;

        vm.successFilter = true;
        vm.dangerFilter = true;
        vm.warningFilter = true;
        vm.primaryFilter = true;

        vm.threeMonths = [];
        vm.whichMonth = {};
        vm.loadUser = loadUser;
        vm.currentMonthIndex = 0;
        vm.dataLoading = false;
        vm.subjectTotalQ = 0;

        vm.currentShow = 0;
        vm.store = $routeParams.id

        initController();

        function initController() {
            //  loadCurrentUser();
            // loadAllUsers();

            //loadMonths();
            loadUser();
            loadToCallCandidates();

        }

        vm.setCurrentMon = function(){
            //console.log("i am in setCurrentMonth",vm.currentMonthIndex);

            vm.whichMonth.name = vm.threeMonths[vm.currentMonthIndex].name;
            vm.whichMonth.num = vm.threeMonths[vm.currentMonthIndex].num;
            console.log("i am in setCurrentMonth",vm.whichMonth);
            loadToCallCandidates();

        }



        vm.logout = function(){
            vm.inUser = null;
            UserService.DeleteInUser();
            $location.path('#/login');
        };

        function loadUser(){
            vm.inUser = UserService.GetInUser();
            if(!vm.inUser.name)
                $location.path('/login');
            console.log("in user",vm.inUser);


        }


        vm.startTest = function(topicId,noOfQuestion,testId,topicName,subjectName,atomic){
            console.log(topicId);
            if(testId)
                $location.path('/test/'+testId+'/result');
            else
                CandidateService.StartTest(vm.inUser.md5,
                    {
                        "topic_id":topicId,
                        "no_of_question":noOfQuestion,
                        "atomic":atomic
                    }
                    )
                    .then(function (response) {
                        vm.subjects = response.response;

                        console.log('member',vm.subjects);

                        $cookieStore.put('tests', JSON.stringify(vm.subjects));
                        $cookieStore.put('topic_name', topicName);
                        $cookieStore.put('subject_name', subjectName);

                        $location.path('/test');
                    });




        }



        vm.loadToCallCandidates = loadToCallCandidates;

        vm.date1 = new Date().getDate();
        vm.getFun = function(work){
            return Math.floor((Math.random() * (work/60/60)) + (work/60/60/4));
        };



        function loadToCallCandidates(){
            vm.dataLoading = true;

            CandidateService.GetStore(vm.inUser.org_id,vm.store)
                .then(function (response) {
                    vm.amounts = response.store_details.amounts;
                    vm.stores = response.store_details.stores;




                    console.log('inside controller',vm.stores);
                });

        }


        function loadAllUsers() {
            UserService.GetAll()
                .then(function (users) {
                    vm.allUsers = users;
                });
        }

        function deleteUser(id) {
            UserService.Delete(id)
                .then(function () {
                    loadAllUsers();
                });
        }





    }

})();