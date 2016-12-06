<?php namespace Ors\Orsapi\Handlers;

use Ors\Orsapi\Interfaces\ObjectInfoInterface;
use Ors\Orsapi\Interfaces\ITAG_SearchApiInterface;
use Ors\Orsapi\Oam\OAMObjectInfo;
use Ors\Orsapi\Oam\OAMGeo;
use Ors\Orsapi\Oam\OAMInfo;
use Ors\Orsapi\Oam\OAMInfoPicture;
use Ors\Orsapi\Oam\OAMRating;
use Ors\Orsapi\Oam\OAMRatingGroup;
use Ors\Orsapi\Oam\OAMMultiFact;
use Ors\Orsapi\Oam\OAMMultiFactItem;
use Ors\Orsapi\OrsApiException;
use Ors\Support\Common;

class ObjectInfoHandler extends SoapApiBaseHandler implements ITAG_SearchApiInterface, ObjectInfoInterface {
	
	/**
	 * @see \Ors\Orsapi\Interfaces\SearchApiInterface::info()
	 */
	public function info($params) {
		$params = $this->toSmartParams($params);
	
	    $this->_makeHeader($params);
	
	    // make api call
	    $call = "orsxml_hotel_api_call";
	    //$call = "orsxml_{$ctype_id}_api_call";
	    $response = $this->orsSoapClient->$call( 'info', $params->__toArray(), $this->header );
	
	    // debug xmlReq
	    Common::ppreDebug( htmlspecialchars($response['xmlReq']), 'xmlReq');
	    //Common::ppre( $response['xmlReq'], 'xmlReq');
	    //Common::ppre( $response, 'Response');
	
	    // check for error
	    $this->_error($response);
	
	    // set request id (rqid)
	    $this->setRqid($response);
	     
	    // set header
	    $this->setApiHeader($response['header']);
	
	    // debug header
	    Common::ppreDebug( $this->header, 'header');
	
	    // create object info model
	    $oi_model = new OAMObjectInfo($response['hotel_info']);
	    if (!empty($response['geo_info']['lat']))
	        $oi_model->lat = $response['geo_info']['lat'];
	    if (!empty($response['geo_info']['lon'])) {
	        $oi_model->lat = $response['geo_info']['lon'];
	        $oi_model->geo = new OAMGeo($response['geo_info']+array('title' => $oi_model->htn, 'content' => $oi_model->htn));
	    }
	
	    // object pictures
	    $pictures = new Collection();
	    if (!empty($response['hotel_info']['pics'])) {
	        foreach ($response['hotel_info']['pics'] as $pic)
	            $pictures->push(new OAMInfoPicture($pic));
	    }
	    $oi_model->pictures = $pictures;
	
	    // create info model
	    $i_model = new OAMInfo(array('toc' => $params->find('toc'), 'gid' => $params->find('gid')));
	    if ($params->has('htc'))
	        $i_model->htc = $params->find('htc');
	    $i_model->object = $oi_model;
	
	    // ratings
	    $r_model = null;
	    if (!empty($response['votes_hc_info']['info']['gid'])) {
	        $r_model = new OAMRating($response['votes_hc_info']['info']);
	        $r_model->groups = new Collection();
	        foreach ($response['votes_hc_info'] as $id => $rgroup) {
	            if ($id == 'info')
	                continue;
	            $r_model->groups->push(new OAMRatingGroup($rgroup+array('id' => $id)));
	        }
	    }
	    $i_model->ratings = $r_model;
	     
	    // characteristics
	    $i_model->characteristics = new Collection();
	    if (!empty($response['characteristics_info'])) {
	        foreach ($response['characteristics_info'] as $c) {
	            $c_model = new OAMMultiFact($c['group']);
	            $c_model->facts = new Collection();
	
	            foreach ($c['items'] as $fact)
	                $c_model->facts->push(new OAMMultiFactItem($fact));
	             
	            $i_model->characteristics->push($c_model);
	        }
	    }
	
	    return $i_model;
	}
	
	/**
	 * @see \Ors\Orsapi\Interfaces\SearchApiInterface::infoToc()
	 */
	public function infoToc($params){
		$params = $this->toSmartParams($params);
	
	    $this->_makeHeader($params);
	
	    // make api call
	    $call = "orsxml_hotel_api_call";
	    //$call = "orsxml_{$ctype_id}_api_call";
	    $response = $this->orsSoapClient->$call( 'info', $params->__toArray(), $this->header );
	
	    // debug xmlReq
	    Common::ppreDebug( htmlspecialchars($response['xmlReq']), 'xmlReq');
	
	    // check for error
	    $this->_error($response);
	     
	    // set request id (rqid)
	    $this->setRqid($response);
	
	    // set header
	    $this->setApiHeader($response['header']);
	
	    // debug
	    Common::ppreDebug( $this->header, 'header');
	     
	    if (empty($response['hotel_info']['gid']))
	        throw new OrsApiException('No description', 0, null, $response['rqid']);
	
	    // create object info model
	    $oi_model = new OAMObjectInfo($response['hotel_info']);
	    if (!empty($response['geo_info']['lat']))
	        $oi_model->lat = $response['geo_info']['lat'];
	    if (!empty($response['geo_info']['lon'])) {
	        $oi_model->lat = $response['geo_info']['lon'];
	        $oi_model->geo = new OAMGeo(array($response['geo_info'])+array('title' => $oi_model->htn, 'content' => $oi_model->htn));
	    }
	
	    // pictures
	    $pictures = new Collection();
	    if (!empty($response['hotel_info']['pics'])) {
	        foreach ($response['hotel_info']['pics'] as $pic)
	            $pictures->push(new OAMInfoPicture($pic));
	    }
	    $oi_model->pictures = $pictures;
	     
	    // add ratings to object info model
	    if (!empty($response['votes_hc_info']['info'])) {
	        $oi_model->ovr = !empty($response['votes_hc_info']['overall']['ovr']) ? $response['votes_hc_info']['overall']['ovr'] : null;
	        $oi_model->emf = !empty($response['votes_hc_info']['info']['emf']) ? $response['votes_hc_info']['info']['emf'] : null;
	    }
	
	    return $oi_model;
	}
	
}