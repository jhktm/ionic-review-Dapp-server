<?php
require 'function.php';
$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {

        case "index":
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res);
            break;

        case "a":
            echo "호호호호호";
            break;

        case "ACCESS_LOGS":
//            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');

            getLogs("./logs/access.log");
            break;
        case "ERROR_LOGS":
//            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');

            getLogs("./logs/errors.log");
            break;


        /*
        * API Name : 테스트 API
        * 마지막 수정 날짜 : 19.05.13
        */
        case "test":
            http_response_code(200);
            $result = test();
            $res->code = 100;
            $res->message = "테스트 성공";

            echo json_encode($result, JSON_NUMERIC_CHECK);

            break;
        /*
       * API Name : 유저만들기
       * 마지막 수정 날짜 : 18.08.16
       */
        case "makeUser":
            $userNo = $req->user_No;
            $name = $req->user_Name;
            $nickname = $req->user_Nickname;
            $tel = $req->user_Tel;
            $mail = $req->user_Mail;
            $img = $req->user_Img;

            userInsert($userNo, $name, $nickname, $tel, $mail,$img);

            $res->code = 100;
            $res->message = "테스트 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);

            break;
        /*
   * API Name : 리뷰작성
   * 마지막 수정 날짜 : 18.08.16
   */
        case "makeReview":
            $title = $req->review_Title;
            $category = $req->review_Category;
            $user = $req->user_No;
            $img = $req->review_Img;
            $satisfaction = $req ->review_Satisfaction;
            $reviewContent = $req ->review_Content;
            $result = reviewInsert($title, $category, $user, $img,$satisfaction,$reviewContent);

            $res->code = 100;
            $res->message = "테스트 성공";

            echo json_encode($result, JSON_NUMERIC_CHECK);

            break;
        /*
* API Name : 댓글달기
* 마지막 수정 날짜 : 19.05.14
*/
        case "writeComment":
            $comment = $req->comment_Content;
            $userno = $req->user_No;
            $reviewno = $req->review_No;
            commentInsert($comment, $userno, $reviewno);
            $res->code = 100;
            $res->message = "테스트 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);

            break;
        /*

  * API Name :리뷰 내용 보여주기
  * 마지막 수정 날짜 : 19.05.13
  */
        case "reviewContent":
            $reviewNo = $vars["reviewno"];

            $res->result = showReviewContent($reviewNo);
            $res->code = "100";
            $res->message = "테스트 성공";

            echo json_encode($res->result, JSON_NUMERIC_CHECK);

            break;
        /*
   * API Name : 리뷰 댓글 보여주기
   * 마지막 수정 날짜 : 19.05.13
   */
        case "reviewComment":
            $reviewNo = $vars["reviewno"];

            $res->result = showReviewComment($reviewNo);
            $res->code = "100";
            $res->message = "테스트 성공";

            echo json_encode($res->result, JSON_NUMERIC_CHECK);

            break;
        /*
* API Name : 유저 보여주기
* 마지막 수정 날짜 : 19.05.13
*/
        case "userAll":
            $userNo = $vars["userno"];

            $res->result = showUser($userNo);
            $res->code = "100";
            $res->message = "테스트 성공";

            echo json_encode($res->result, JSON_NUMERIC_CHECK);

            break;
        /*
* API Name : 유저 보여주기
* 마지막 수정 날짜 : 19.05.13
*/
        case "searchReview":
            $reviewTitle = $vars["reviewsearch"];

            $res->result = searchReview(preg_replace("/\s+/","",$reviewTitle));
            $res->code = "100";
            $res->message = "테스트 성공";

            echo json_encode($res->result, JSON_NUMERIC_CHECK);

            break;
        /*
    * API Name : 리뷰삭제
    * 마지막 수정 날짜 : 18.08.16
    */
        case "reviewDelete":
            $reviewNo = $req->review_No;

            deleteReviewContent($reviewNo);
            $res->code = "100";
            $res->message = "테스트 성공";

            echo json_encode( $res, JSON_NUMERIC_CHECK);

            break;
        /*
    * API Name : 유저삭제
    * 마지막 수정 날짜 : 18.08.16
    */
        case "userDelete":
            $userNo = $req->review_No;

            deleteUserInfo($userNo);
            $res->code = "100";
            $res->message = "테스트 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);

            break;
            /*
* API Name : 댓글삭제
* 마지막 수정 날짜 : 18.08.16
*/
        case "commentDelete":
            $commentNo = $req->comment_No;

            deleteCommentContent($commentNo);
            $res->code = "100";
            $res->message = "테스트 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);

            break;
        /*
      * API Name : 눈 리뷰 보여주기
      * 마지막 수정 날짜 : 19.05.13
      */
        case "reviewEyes":
            $result = showReviewEyes();
            $res->code = "100";
            $res->message = "테스트 성공";

            echo json_encode($result, JSON_NUMERIC_CHECK);

            break;
        /*
  * API Name : 가슴 리뷰 보여주기
  * 마지막 수정 날짜 : 19.05.13
  */
        case "reviewBreast":
            $result = showReviewBreast();
            $res->code = "100";
            $res->message = "테스트 성공";

            echo json_encode($result, JSON_NUMERIC_CHECK);

            break;
        /*
* API Name : 코 리뷰 보여주기
* 마지막 수정 날짜 : 19.05.13
*/
        case "reviewNose":
            $result = showReviewNose();
            $res->code = "100";
            $res->message = "테스트 성공";

            echo json_encode($result, JSON_NUMERIC_CHECK);

            break;
        /*
* API Name : 안면 리뷰 보여주기
* 마지막 수정 날짜 : 19.05.13
*/
        case "reviewFace":
            $result = showReviewFace();
            $res->code = "100";
            $res->message = "테스트 성공";

            echo json_encode($result, JSON_NUMERIC_CHECK);

            break;
        /*
* API Name : 몸 리뷰 보여주기
* 마지막 수정 날짜 : 19.05.13
*/
        case "reviewBody":
            $result = showReviewBody();
            $res->code = "100";
            $res->message = "테스트 성공";

            echo json_encode($result, JSON_NUMERIC_CHECK);

            break;
        /*
        * API Name : 기타 리뷰 보여주기
        * 마지막 수정 날짜 : 19.05.13
        */
        case "reviewEtc":
            $result = showReviewEtc();
            $res->code = "100";
            $res->message = "테스트 성공";

            echo json_encode($result, JSON_NUMERIC_CHECK);

            break;
        /*
              * API Name : BEST 리뷰 보여주기
              * 마지막 수정 날짜 : 19.05.13
              */
        case "reviewBest":
            $result = showBestReview();
            $res->code = "100";
            $res->message = "테스트 성공";

            echo json_encode($result, JSON_NUMERIC_CHECK);

            break;

        /*
          * API Name : 리뷰 LIKE
          * 마지막 수정 날짜 : 19.05.14
          */
        case "makeReviewLike":
            $reviewno = $req->review_No;
            $userno = $req->user_No;

            makeLike($reviewno, $userno, 1);
            $res->code = 100;
            $res->message = "테스트 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);

            break;
        /*
     * API Name : 댓글 LIKE
     * 마지막 수정 날짜 : 19.05.14
     */
        case "makeCommentLike":
            $commentno = $req->comment_No;
            $userno = $req->user_No;

            makeLike($commentno, $userno, 2);
            $res->code = 100;
            $res->message = "테스트 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);

            break;

        case "reviewCount":

            $review_Count=reviewCount();
            $res->code = 100;
            $res->message = "테스트 성공";

            echo json_encode($review_Count, JSON_NUMERIC_CHECK);

            break;
        /*
  * API Name : 이벤트 보여주기
  * 마지막 수정 날짜 : 19.05.13
  */
        case "eventAll":
            $result = showEvent();
            $res->code = "100";
            $res->message = "테스트 성공";

            echo json_encode($result, JSON_NUMERIC_CHECK);

            break;

        case "fileUpload":
            $file_name = $_FILES['upload_file']['name'];                // 업로드한 파일명
            $file_tmp_name = $_FILES['upload_file']['tmp_name'];   // 임시 디렉토리에 저장된 파일명
            $file_size = $_FILES['upload_file']['size'];                 // 업로드한 파일의 크기
            $mimeType = $_FILES['upload_file']['type'];                 // 업로드한 파일의 MIME Type
// 첨부 파일이 저장될 서버 디렉토리 지정(원하는 경로에 맞게 수정하세요)

            $save_dir = './img/profile/';



// 업로드 파일 확장자 검사 (필요시 확장자 추가)

            if($mimeType=="html" ||

                $mimeType=="htm" ||

                $mimeType=="php" ||

                $mimeType=="php3" ||

                $mimeType=="inc" ||

                $mimeType=="pl" ||

                $mimeType=="cgi" ||

                $mimeType=="txt" ||

                $mimeType=="TXT" ||

                $mimeType=="asp" ||

                $mimeType=="jsp" ||

                $mimeType=="phtml" ||

                $mimeType=="js" ||

                $mimeType=="") {

                echo("업로드할수 없는 파일형식");

                exit;

            }
            // 파일명 변경 (업로드되는 파일명을 별도로 생성하고 원래 파일명을 별도의 변수에 지정하여 DB에 기록할 수 있습니다.)
            $real_name = $file_name;     // 원래 파일명(업로드 하기 전 실제 파일명)

            $arr = explode(".", $real_name);	 // 원래 파일의 확장자명을 가져와서 그대로 적용 $file_exe

            $arr1 = $arr[0];
            $arr2 = $arr[1];
            $arr3 = $arr[2];
            $arr4 = $arr[3];
            if($arr4) {

                $file_exe = $arr4;

            } else if($arr3 && !$arr4) {

                $file_exe = $arr3;

            } else if($arr2 && !$arr3) {

                $file_exe = $arr2;

            }
            $file_time = time();

            $file_Name = "file_".$file_time.".".$file_exe;	 // 실제 업로드 될 파일명 생성	(본인이 원하는 파일명 지정 가능)

            $change_file_name = $file_Name;			 // 변경된 파일명을 변수에 지정

            $real_name = addslashes($real_name);		// 업로드 되는 원래 파일명(업로드 하기 전 실제 파일명)

            $real_size = $file_size;                         // 업로드 되는 파일 크기 (byte)

//파일을 저장할 디렉토리 및 파일명 전체 경로
            $dest_url = $save_dir . $change_file_name;
//파일을 지정한 디렉토리에 업로드
            if(!move_uploaded_file($file_tmp_name, $dest_url))

            {
                die("파일을 지정한 디렉토리에 업로드하는데 실패했습니다.");

            }

// DB에 기록할 파일 변수 (DB에 저장이 필요한 경우 아래 변수명을 기록하시면 됩니다.)

            /*

                $change_file_name : 실제 서버에 업로드 된 파일명. 예: file_145736478766.gif

                $real_name : 원래 파일명. 예: 풍경사진.gif

                $real_size : 파일 크기(byte)

            */

            $res->code = 100;
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
    }

} catch (Exception $e) {

    return getSQLErrorException($errorLogs, $e, $req);
}