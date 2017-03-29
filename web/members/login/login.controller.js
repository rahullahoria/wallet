﻿(function () {
    'use strict';

    angular
        .module('app')
        .controller('LoginController', LoginController);

    LoginController.$inject = ['$window','$location', 'UserService', '$cookieStore','CandidateService', 'AuthenticationService', 'FlashService'];
    function LoginController($window, $location, UserService, $cookieStore,CandidateService, AuthenticationService, FlashService) {
        var vm = this;

        vm.login = login;
        vm.user = {};
        vm.plans = {s:{name:'Silver',price:1000},g:{name:'Gold',price:2000},p:{name:'Platinum',price:5000}};
        vm.user.username = "";
        vm.user.password = "";
        vm.inUser = null;
        vm.regR = ($location.search().rt != undefined)?true:false;
        vm.user.ref_username = ($location.search().ref_user != undefined)?$location.search().ref_user:'';
        vm.user.enable_buy_plan = ($location.search().bp != undefined)?true:false;
        vm.user.buy_plan = ($location.search().bp != undefined)?$location.search().bp:'';
        if(vm.user.ref_username){
            vm.user.enable_ref_username = false;
        }
        else
        vm.user.enable_ref_username = true;
        console.log('rt', $location.search().rt);

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



        vm.reg = function(){
            vm.dataLoadingReg = true;
            CandidateService.Create(vm.user
                )
                .then(function (response) {
                    console.log("resp",response);

                    if (response.results && response.results.id) {
                        AuthenticationService.SetCredentials(vm.user.reg_username, vm.user.reg_password);
                        vm.inUser = response.results;
                        vm.inUser.username = vm.inUser.reg_username;
                        $cookieStore.put('inUser', JSON.stringify(vm.inUser));
                        vm.dataLoadingReg = false;

                        vm.showVerification = true;

                        console.log("auth success in user",vm.inUser);
                        //$location.path('/member');

                    } else {
                        FlashService.Error(response.error.text);
                        vm.regError = response.error.text;
                        vm.dataLoadingReg = false;
                    }
                });

            console.log(vm.user);
        };

        vm.startDemoTest = function(){
            CandidateService.StartDemoTest(vm.inUser.md5
                )
                .then(function (response) {
                    vm.subjects = response.response;

                    console.log('member',vm.subjects);

                    $cookieStore.put('tests', JSON.stringify(vm.subjects));
                    $cookieStore.put('topic_name', 'Mix Topics');
                    $cookieStore.put('subject_name', 'All Subjects');

                    $location.path('/test');
                });
        }

        vm.checkOTP = function(type){
            if(type == 'skip'){
                $("#instructionsModel").modal("show");
            }else
          CandidateService.CheckOTP(vm.inUser.md5,type,vm.user[type+'_otp']
              )
              .then(function (response) {
                  console.log("resp",response);

                  if (response.auth == "true") {
                      alert('Verified Successfully');
                      if(vm.user.enable_buy_plan){
                         // console.log(response.user.username );

                          $window.location.href = "https://examhans.com/payment/index.php?amount="+
                              vm.plans[vm.user.buy_plan].price + '&firstname=' +
                              response.user.username + '&email=' +
                              vm.user.email + '&phone=' +
                              vm.user.mobile + '&productinfo=' +
                              vm.plans[vm.user.buy_plan] + "&surl=https://examhans.com/payment/success.php&furl=https://examhans.com/payment/failure.php";



                      }else {
                          vm.user[type + '_verified'] = true;
                          $("#instructionsModel").modal("show");
                          if (vm.user.sms_verified == true && vm.user.email_verified == true) {
                              //show model
                              $("#instructionsModel").modal("show");


                          }
                      }
                  } else {
                      alert('Don\'t Match Please Try Again!');
                      FlashService.Error(response.error.text);
                      vm.dataLoading = false;
                  }
              });

            console.log(vm.user);
        };


        function login() {
            vm.dataLoading = true;
            AuthenticationService.Login(vm.user, function (resp) {
                console.log("resp",resp);

                if (resp.success) {
                    AuthenticationService.SetCredentials(vm.user.username, vm.user.password);
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
