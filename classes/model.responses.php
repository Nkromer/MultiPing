<?php

class Model_UserNotification
{
	/* Private variables */	 
	 private $last_error = "";
	 
	 private $dbl; //The DBL Reference
	 
	 /* Constructor */
	 public function __construct(&$dbl_ref){
        
        //Set proper type_name
        $this->type_name = __CLASS__;
        
        //Set the pointer of the static DB class
        $this->dbl = $dbl_ref;
        
    }

   /**
    *
    * @param int $ping_request_id
    * @return int
    */
   public function fetchNumberByRequestId($recipientUid)
   {
       return $this->_db->fetch("SELECT count(*) as count "
           . " FROM notification WHERE recipientUid = %d AND isNew = 1"
           , $recipientUid)->count;
   }
   /**
    *
    * @param int $recipientUid
    * @param int $eventId
    */
   public function add($recipientUid, $eventId)
   {
       $this->_db->update("INSERT INTO "
           . " notification (`id`, `recipientUid`, `eventId`, `isNew`) VALUES (NULL, '%d', '%d', '1')"
           , $recipientUid, $eventId);
   }
   /**
    *
    * @param int $recipientUid
    */
   public function removeAll($recipientUid)
   {
       $this->_db->update("DELETE FROM "
           . " notification WHERE recipientUid = %d"
           , $recipientUid);
   }
}

?>