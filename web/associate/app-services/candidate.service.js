/**
 * Created by spider-ninja on 6/4/16.
 */
(function () {
    'use strict';

    angular
        .module('app')
        .factory('CandidateService', CandidateService);

    CandidateService.$inject = ['$http'];

    function CandidateService($http) {
        var service = {};

        service.GetExams = GetExams;
        service.Create = Create;
        service.Update = Update;
        service.Delete = Delete;
        service.GetUserInstance = GetUserInstance;
        service.UpdateInstance = UpdateInstance;
        service.GetStatus = GetStatus;
        service.StartTest = StartTest;
        service.GetQuestion = GetQuestion;
        service.SubmitRespnse = SubmitRespnse;
        service.ShowResults = ShowResults;
        service.GetTopicMatter = GetTopicMatter;
        service.CheckOTP = CheckOTP;
        service.StartDemoTest = StartDemoTest;
        service.postAccount = postAccount;
        service.GetMoney = GetMoney;


        return service;



        function GetMoney(payment){
            var form_data = new FormData();

            for ( var key in payment ) {
                form_data.append(key, payment[key]);
            }
            return $http({
                method  : 'POST',
                url     : 'https://examhans.com/payment/index.php',
                data    : form_data, //forms user object
                headers : {'Content-Type': 'application/x-www-form-urlencoded'}
            });
        }

        function GetStatus(userMD5) {
            return $http
                .get('https://api.examhans.com/user/'+userMD5+'/status')
                .then(handleSuccess, handleError('Error getting all users'));
        }

        function GetTopicMatter(topicId) {
            return $http
                .get('https://api.examhans.com/topics/'+topicId+'/videos')
                .then(handleSuccess, handleError('Error getting all users'));
        }

        function ShowResults(userMD5,testId) {
            return $http
                .get('https://api.examhans.com/user/'+userMD5+'/test/'+testId+'/result')
                .then(handleSuccess, handleError('Error getting all users'));
        }

        function GetUserInstance(professionId,uType,month) {
            return $http
                .get('https://api.bulldog.shatkonlabs.com/profession/'+professionId+'/type/'+uType+"/instance?month=" +month)
                .then(handleSuccess, handleError('Error getting all users'));
        }





        function GetQuestion(userMd5,testId,id) {
            return $http.get('https://api.examhans.com/user/'+userMd5+'/test/'+testId+'/goto/' + id).then(handleSuccess, handleError('Error getting user by id'));
        }



        function GetExams(str) {
            return $http.get('https://api.examhans.com/exams' ).then(handleSuccess, handleError('Error getting user by username'));
        }

        function CheckOTP(userMd5,type,otp) {
            return $http.get('https://api.examhans.com/user/'+userMd5+'/verify/'+type+'/otp/'+otp ).then(handleSuccess, handleError('Error getting user by username'));
        }

        function Create(user) {
            return $http.post('https://api.examhans.com/user', user).then(handleSuccess, handleError('Error creating user'));
        }

        function Update(user) {
            return $http.put('https://api.shatkonjobs.com/candidates/' + user.id, user).then(handleSuccess, handleError('Error updating user'));
        }

        function UpdateInstance(instance) {
            return $http.post('https://api.bulldog.shatkonlabs.com/instance', instance).then(handleSuccess, handleError('Error updating user'));
        }

        function SubmitRespnse(userMd5,testId,responseId,instance) {
            ///user/:userMd5/test/:testId/question/:responseId
            return $http.post('https://api.examhans.com/user/'+userMd5+'/test/'+testId+'/question/'+responseId, instance).then(handleSuccess, handleError('Error updating user'));
        }

        function StartTest(userMd5, instance) {
            return $http.post('https://api.examhans.com/user/'+userMd5+'/test/', instance).then(handleSuccess, handleError('Error updating user'));
        }

        function postAccount(userMd5, instance) {
            return $http.post('https://api.examhans.com/user/'+userMd5+'/bank_account', instance).then(handleSuccess, handleError('Error updating user'));
        }

        function StartDemoTest(userMd5) {
            return $http.post('https://api.examhans.com/user/'+userMd5+'/demo_test', {}).then(handleSuccess, handleError('Error updating user'));
        }

        function Delete(id) {
            return $http.delete('/api/users/' + id).then(handleSuccess, handleError('Error deleting user'));
        }

        // private functions

        function handleSuccess(res) {
            return res.data;
        }

        function handleError(error) {
            return function () {
                return { success: false, message: error };
            };
        }
    }

})();
