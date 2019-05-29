<?php

require 'pdoConnect.php';
function test()
{
    $pdo = pdoSqlConnect();

    $query = "select U.user_No,U.user_Nickname,user_Img,IFNULL(F.countL,0) as comment_N,review_Title,review_No,review_Like,review_Date ,review_Category ,review_Satisfaction,review_Img 
        from User_TB as U
        join (SELECT R.*, count(C.comment_No)as countL 
            FROM (select * from Review_TB where review_Status='1') as R 
                left outer join (select * from Comment_TB where comment_Status='1') as C 
                on R.review_No= C.review_No  group by R.review_No)as F 
        on U.user_No = F.user_No
        order by review_Date desc";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;

}//리뷰 보여주기
function userInsert($userNo, $username, $nickname, $userTel, $mail,$img)
{
    $pdo = pdoSqlConnect();
    $query = "select user_No as N from User_TB where user_No=:userno;";
    $st = $pdo->prepare($query);
    $st->bindParam(':userno', $userNo);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetch();

    if ($res["N"] == $userNo) {
        if($img){
            $query = "update User_TB 
              set user_No =:userno ,user_Name=:username, user_Nickname=:nickname, user_Tel=:tel ,user_Mail=:mail,user_Img=:img
              where user_No= :userno";
            $st = $pdo->prepare($query);
            $st->bindParam(':userno', $userNo);
            $st->bindParam(':img', $img);
            $st->bindParam(':username', $username);
            $st->bindParam(':nickname', $nickname);
            $st->bindParam(':tel', $userTel);
            $st->bindParam(':mail', $mail);
        }else{
            $query = "update User_TB 
              set user_No =:userno ,user_Name=:username, user_Nickname=:nickname, user_Tel=:tel ,user_Mail=:mail
              where user_No= :userno";
            $st = $pdo->prepare($query);
            $st->bindParam(':userno', $userNo);
            $st->bindParam(':username', $username);
            $st->bindParam(':nickname', $nickname);
            $st->bindParam(':tel', $userTel);
            $st->bindParam(':mail', $mail);
        }
        $st->execute();
    } else {
        $query = "Insert into Review_DB.User_TB(user_No,user_Name,user_Nickname,user_Tel,user_Mail) 
              values(:userno,:username,:nickname,:tel,:mail)";
        $st = $pdo->prepare($query);
        //    $st->execute([$param,$param]);
        $st->bindParam(':userno', $userNo);
        $st->bindParam(':username', $username);
        $st->bindParam(':nickname', $nickname);
        $st->bindParam(':tel', $userTel);
        $st->bindParam(':mail', $mail);
        $st->execute();
    }

    $st = null;
    $pdo = null;

}//유저
function reviewInsert($title, $category, $userNo, $img, $satisfaction,$reviewContent)
{
    $pdo = pdoSqlConnect();
    $query = "Insert into Review_DB.Review_TB(review_Title, review_Category,User_No,review_Img, review_Satisfaction,review_Content) 
              values(:title,:category,:userno,:img, :satisfaction,:reviewContent);";

    $st = $pdo->prepare($query);
    $st->bindParam(':title', $title);
    $st->bindParam(':category', $category);
    $st->bindParam(':userno', $userNo);
    $st->bindParam(':img', $img);
    $st->bindParam(':satisfaction', $satisfaction);
    $st->bindParam(':reviewContent', $reviewContent);

    $st->execute();

    $query = "select review_No, review_Satisfaction from Review_TB where user_No=:userno order by review_Date desc limit 1";
    $st = $pdo->prepare($query);
    $st->bindParam(':userno', $userNo);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetch();
    $st = null;
    $pdo = null;

    return $res;


}//리뷰작성
function commentInsert($commentContent, $userNo, $reviewNo)
{
    $pdo = pdoSqlConnect();
    $query = "Insert into Review_DB.Comment_TB(comment_Content, user_No,review_No)
              values(:commentcontent,:userno,:reviewno);";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->bindParam(':commentcontent', $commentContent);
    $st->bindParam(':userno', $userNo);
    $st->bindParam(':reviewno', $reviewNo);

    $st->execute();
    $st = null;
    $pdo = null;
}//댓글작성
function makeLike($postNo, $userNo, $postType)
{

    $pdo = pdoSqlConnect();
    $query = "select count(*) as countL from Like_TB where user_No= :userno and post_No = :postno and post_Type = :posttype";
    $st = $pdo->prepare($query);
    $st->bindParam(':postno', $postNo);
    $st->bindParam(':userno', $userNo);
    $st->bindParam(':posttype', $postType);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetch();

    if ($postType == 2) {
        if ($res["countL"] == 0) {//입력
            $query = "Insert into Review_DB.Like_TB(post_No,user_No,post_Type)
              values (:postno,:userno,:posttype)";

            $st = $pdo->prepare($query);
            //    $st->execute([$param,$param])
            $st->bindParam(':postno', $postNo);
            $st->bindParam(':userno', $userNo);
            $st->bindParam(':posttype', $postType);
            $st->execute();
        } else {
            $query = "delete from Like_TB where user_No= :userno and post_No = :postno and post_Type = :posttype";
            $st = $pdo->prepare($query);
            $st->bindParam(':postno', $postNo);
            $st->bindParam(':userno', $userNo);
            $st->bindParam(':posttype', $postType);
            $st->execute();
        }
    } else if ($postType == 1) {
        if ($res["countL"] == 0) {//입력
            $query = "Insert into Review_DB.Like_TB(post_No,user_No,post_Type)
              values (:postno,:userno,:posttype)";

            $st = $pdo->prepare($query);
            //    $st->execute([$param,$param])
            $st->bindParam(':postno', $postNo);
            $st->bindParam(':userno', $userNo);
            $st->bindParam(':posttype', $postType);
            $st->execute();
        } else {
            $query = "delete from Like_TB where user_No= :userno and post_No = :postno and post_Type = :posttype";
            $st = $pdo->prepare($query);
            $st->bindParam(':postno', $postNo);
            $st->bindParam(':userno', $userNo);
            $st->bindParam(':posttype', $postType);
            $st->execute();
        }
    }


    //댓글에 대한 좋아요 갯수세기
    $query = "SELECT count(*) as countLike FROM Like_TB  where  post_No = :postno and post_Type = :posttype";
    $st = $pdo->prepare($query);
    $st->bindParam(':postno', $postNo);
    $st->bindParam(':posttype', $postType);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetch();

    $count = $res["countLike"];

    if ($postType == 1) {
        //Review_TB에 좋아요 숫자 올려주기
        $query = "UPDATE Review_TB set review_Like = :countlike where review_No = :postno ";
        $st = $pdo->prepare($query);
        $st->bindParam(':postno', $postNo);
        $st->bindParam(':countlike', $count);
        $st->execute();

    } else if ($postType == 2) {
        //comment_TB에 좋아요 숫자 올려주기
        $query = "UPDATE Comment_TB set comment_Like = :countlike where comment_No = :postno ";
        $st = $pdo->prepare($query);
        $st->bindParam(':postno', $postNo);
        $st->bindParam(':countlike', $count);
        $st->execute();

    }
    $st = null;
    $pdo = null;
    return;
}//좋아요 만들기

function showReviewEyes()
{
    $pdo = pdoSqlConnect();
    $query = "select U.user_No,U.user_Nickname,user_Img,IFNULL(F.countL,0) as comment_N,review_Title,review_No,review_Like,review_Date ,review_Category ,review_Satisfaction,review_Img
        from User_TB as U
        join (SELECT R.*, count(C.comment_No)as countL FROM (select * from Review_TB where review_Category ='eyes' and review_Status='1') as R 
            left outer join (select * from Comment_TB where comment_Status='1') as C on R.review_No= C.review_No  group by R.review_No)as F 
             on U.user_No = F.user_No
        order by review_Date desc";

    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;

    return $res;
}//눈
function showReviewBreast()
{
    $pdo = pdoSqlConnect();
    $query = "select U.user_No,U.user_Nickname,user_Img,IFNULL(F.countL,0) as comment_N,review_Title,review_No,review_Like,review_Date ,review_Category,review_Satisfaction,review_Img
        from User_TB as U
        join (SELECT R.*, count(C.comment_No)as countL FROM (select * from Review_TB where review_Category ='breast'and review_Status='1') as R 
            left outer join (select * from Comment_TB where comment_Status='1') as C on R.review_No= C.review_No  group by R.review_No)as F 
            on U.user_No = F.user_No
        order by review_Date desc";

    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;

    return $res;
}//가슴
function showReviewNose()
{
    $pdo = pdoSqlConnect();
    $query = "select U.user_No,U.user_Nickname,user_Img,IFNULL(F.countL,0) as comment_N,review_Title,review_No,review_Like,review_Date ,review_Category,review_Satisfaction,review_Img 
        from User_TB as U
        join (SELECT R.*, count(C.comment_No)as countL FROM (select * from Review_TB where review_Category ='nose'and review_Status='1') as R 
            left outer join (select * from Comment_TB where comment_Status='1') as C on R.review_No= C.review_No  group by R.review_No)as F 
            on U.user_No = F.user_No
        order by review_Date desc";

    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;


    return $res;
}//코
function showReviewFace()
{
    $pdo = pdoSqlConnect();
    $query = "select U.user_No,U.user_Nickname,user_Img,IFNULL(F.countL,0) as comment_N,review_Title,review_No,review_Like,review_Date ,review_Category,review_Satisfaction,review_Img 
        from User_TB as U
        join (SELECT R.*, count(C.comment_No)as countL FROM (select * from Review_TB where review_Category ='face'and review_Status='1') as R 
            left outer join (select * from Comment_TB where comment_Status='1') as C on R.review_No= C.review_No  group by R.review_No)as F 
            on U.user_No = F.user_No
        order by review_Date desc";

    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;

    return $res;
}//안면
function showReviewBody()
{
    $pdo = pdoSqlConnect();
    $query = "select U.user_No,U.user_Nickname,user_Img,IFNULL(F.countL,0) as comment_N,review_Title,review_No,review_Like,review_Date ,review_Category,review_Satisfaction,review_Img
        from User_TB as U
        join (SELECT R.*, count(C.comment_No)as countL FROM (select * from Review_TB where review_Category ='body'and review_Status='1') as R 
             left outer join (select * from Comment_TB where comment_Status='1') as C on R.review_No= C.review_No  group by R.review_No)as F 
             on U.user_No = F.user_No
        order by review_Date desc";

    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;

    return $res;
}//몸
function showReviewEtc()
{
    $pdo = pdoSqlConnect();
    $query = "select U.user_No,U.user_Nickname,user_Img,IFNULL(F.countL,0) as comment_N,review_Title,review_No,review_Like,review_Date ,review_Category ,review_Satisfaction,review_Img
        from User_TB as U
        join (SELECT R.*, count(C.comment_No)as countL FROM (select * from Review_TB where review_Category ='etc'and review_Status='1') as R 
               left outer join (select * from Comment_TB where comment_Status='1') as C on R.review_No= C.review_No  group by R.review_No)as F 
              on U.user_No = F.user_No
        order by review_Date desc";

    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;

    return $res;
}//기타
function showBestReview()
{
    $pdo = pdoSqlConnect();
    $query = "select U.user_No,U.user_Nickname,user_Img,IFNULL(F.countL,0) as comment_N,review_Title,review_No,review_Like,review_Date ,review_Category,review_Img,review_Satisfaction  
        from User_TB as U
        join (SELECT R.*, count(C.comment_No)as countL 
            FROM (select * from Review_TB where review_Status='1'order by review_Like desc limit 3) as R 
                left outer join (select * from Comment_TB where comment_Status='1') as C 
                on R.review_No= C.review_No  group by R.review_No)as F 
        on U.user_No = F.user_No
        order by review_Like desc";

    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;

    return $res;
}//best 리뷰보기
function showReviewContent($reviewNo)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM Review_TB where  review_No =:reviewno and review_Status=1";

    $st = $pdo->prepare($query);
    $st->bindParam(':reviewno', $reviewNo);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetch();
    $st = null;
    $pdo = null;

    return $res;
}//리뷰보여주기
function showUser($userNo)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM User_TB where  user_No =:userno";

    $st = $pdo->prepare($query);
    $st->bindParam(':userno', $userNo);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetch();
    $st = null;
    $pdo = null;

    return $res;
}//user보여주기
////////////////////////////////////////////////////////////
function searchReview($reviewTitle)
{
    $pdo = pdoSqlConnect();
    $str= "%".$reviewTitle."%";
    $query = "select U.user_No,U.user_Nickname,user_Img,IFNULL(F.countL,0) as comment_N,review_Title,review_No,review_Like,review_Date ,review_Category  
        from (select * from User_TB where  replace(user_Nickname,' ','') like :search) as U
        join (SELECT R.*, count(C.comment_No)as countL  
            FROM (select * from Review_TB where review_Status='1' and  replace(review_Title,' ', '') like :search or replace(review_Content,' ','') like :search,) as R 
                left outer join (select * from Comment_TB where comment_Status='1') as C 
                on R.review_No= C.review_No  group by R.review_No)as F 
        on U.user_No = F.user_No
        order by review_Date desc";


    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->bindParam(':search', $str);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;

}//검색//////////////////////////////////////////////////////////
function showReviewComment($reviewNo)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT C.*, U.user_Nickname FROM Comment_TB as C join User_TB as U on C.user_No = U.user_No 
              where  review_No =:reviewno and comment_Status =1 order by comment_Date desc";

    $st = $pdo->prepare($query);
    $st->bindParam(':reviewno', $reviewNo);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;

    return $res;

}//댓글 보여주기
function showEvent()
{
    $pdo = pdoSqlConnect();
    $query = "select * from Hospital_TB as H join Advertise_TB as A on H.hospital_No = A.hospital_No";

    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;

    return $res;

}//이벤트 보여주기

function deleteReviewContent($reviewNo)
{
    $pdo = pdoSqlConnect();
    $query = "update Review_TB set review_Status ='0' where  review_No =:reviewno";

    $st = $pdo->prepare($query);
    $st->bindParam(':reviewno', $reviewNo);
    $st->execute();

    $st = null;
    $pdo = null;
}//리뷰 삭제 상태
function deleteUserInfo($userNo)
{
    $pdo = pdoSqlConnect();
    $query = "update User_TB set user_Status ='0' where  user_No =:userno";

    $st = $pdo->prepare($query);
    $st->bindParam(':userno', $userNo);
    $st->execute();

    $st = null;
    $pdo = null;
}//유저 정보 삭제 상태
function deleteCommentContent($commentNo)
{
    $pdo = pdoSqlConnect();
    $query = "update Comment_TB set comment_Status ='0' where  comment_No =:commentno";

    $st = $pdo->prepare($query);
    $st->bindParam(':commentno', $commentNo);
    $st->execute();

    $st = null;
    $pdo = null;
}// 댓글 삭제 상태

function reviewCount()
{
    $pdo = pdoSqlConnect();
    $query = "SELECT count(review_No) FROM Review_TB where review_Status =1";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;

    return $res;
}
