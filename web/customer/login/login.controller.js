(function () {
    'use strict';

    angular
        .module('app')
        .controller('LoginController', LoginController);

    LoginController.$inject = ['$window','$location', 'UserService', '$cookieStore','CandidateService', 'AuthenticationService', 'FlashService'];
    function LoginController($window, $location, UserService, $cookieStore,CandidateService, AuthenticationService, FlashService) {
        var vm = this;

        vm.login = login;
        vm.user = {};
        vm.user.username = "";
        vm.user.password = "";
        vm.inUser = null;


        (function initController() {
            // reset login status
            //vm.inUser = UserService.GetInUser();
            if(vm.inUser){

                    $location.path('/member');
            }else{

                CandidateService.GetExams(''
                    )
                    .then(function (response) {
                        console.log("resp",response);

                        vm.exams = response.exams;
                        console.log("exams",vm.exams);
                    });

            AuthenticationService.ClearCredentials();
            }
        })();


        vm.verMob = function(){
            if(vm.takePs){
                vm.login();
            }else
            CandidateService.GetMobileStatus(vm.user.mobile)
                .then(
                    function(resp){
                        console.log("resp",resp);
                        if(resp.password == 'false'){
                            vm.showVerification = true;
                        }
                        else
                            vm.takePs = true;
                    }
                );
        };

        vm.reg = function(){
            vm.dataLoadingReg = true;
            CandidateService.Create(vm.user.mobile,vm.user
                )
                .then(function (response) {
                    console.log("resp",response);

                    if (response.results && response.results.rows == 1) {
                        AuthenticationService.SetCredentials(vm.user.mobile, vm.user.password);
                        vm.inUser = response.results;
                        vm.inUser.username = vm.inUser.mobile;
                        $cookieStore.put('inUser', JSON.stringify(vm.inUser));
                        vm.dataLoadingReg = false;

                        vm.showVerification = true;

                        console.log("auth success in user",vm.inUser);
                        $location.path('/member');

                    } else {
                        alert("OTP don't Match!");
                        vm.dataLoadingReg = false;
                    }
                });

            console.log(vm.user);
        };






        function login() {
            vm.dataLoading = true;

            AuthenticationService.Login(vm.user, function (resp) {
                console.log("resp",resp);

                if (resp.success) {
                    AuthenticationService.SetCredentials(vm.user.mobile, vm.user.password);
                    vm.inUser = UserService.GetInUser();

                    console.log("auth success");

                        $location.path('/member');

                } else {
                    FlashService.Error(resp.message);
                    vm.dataLoading = false;
                }
            });
        };
    }

})();
