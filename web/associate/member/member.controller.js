(function () {
    'use strict';

    angular
        .module('app')
        .controller('MemberController', MemberController);

    MemberController.$inject = ['UserService', '$cookieStore', 'CandidateService', '$rootScope', 'FlashService','$location'];
    function MemberController(UserService, $cookieStore, CandidateService,  $rootScope, FlashService,$location) {
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

        initController();

        function initController() {
          //  loadCurrentUser();
           // loadAllUsers();

            //loadMonths();
            loadUser();
            //loadToCallCandidates();

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


        vm.date1 = new Date().getDate();



        vm.transaction = function(){
            vm.user.associate_id = vm.inUser.id;



                CandidateService.Tran(vm.inUser.org_id,vm.user)
                    .then(function (response) {
                        console.log("resp",response);

                        vm.showVerification = true;
                    });

        }

        vm.checkOTP = function(type){

                CandidateService.CheckOTP(vm.inUser.org_id,vm.user.mobile,vm.user.otp
                    )
                    .then(function (response) {
                        console.log("resp",response);

                        if (response.auth == "true") {
                            alert('Verified Successfully');

                        } else {
                            alert('Don\'t Match Please Try Again!');
                            FlashService.Error(response.error.text);
                            vm.dataLoading = false;
                        }
                    });

            console.log(vm.user);
        };




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