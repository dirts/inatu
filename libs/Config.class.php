<?php
namespace Inauth\Libs;

class Config extends \Frame\Config {

    private $confNamespace;

    function __construct($env) {
        parent::__construct();
        $this->confNamespace = '\\Inauth\\Config\\' . $env . '\\';
        $this->setDb();
        $this->setRedis();
        $this->setSessionRedis();
        $this->setWebSessionRedis();
        $this->setMemcache();
        $this->setHMemcache();
        $this->setRemote();
        $this->setKafka();
        $this->setScribe();
        $this->setSessionRelation();
        $this->setMainSiteRedis();
        $this->setInternal();
        $this->setIdc();
    }

    private function setDb() {
        $mysqlConfig = $this->confNamespace . 'MySQL';
        $this->db = function () use ($mysqlConfig) {
            return $mysqlConfig::instance()->configs();
        };
    }

    private function setRedis() {
        $redisConfig = $this->confNamespace . 'Redis';
        $this->redis = function () use ($redisConfig) {
            return $redisConfig::instance()->configs();
        };
    }
    
    private function setSessionRedis() {
        $redisSConfig = $this->confNamespace . 'SessionRedis';
        $this->sredis = function () use ($redisSConfig) {
            return $redisSConfig::instance()->configs();
        };
    }
    
    private function setWebSessionRedis() {
        $redisSConfig = $this->confNamespace . 'SessionRedisWeb';
        $this->wredis = function () use ($redisSConfig) {
            return $redisSConfig::instance()->configs();
        };
    }

    private function setMemcache() {
        $memcacheConfig = $this->confNamespace . 'Memcache';
        $this->memcache = function () use ($memcacheConfig) {
            return $memcacheConfig::instance()->configs();
        };
    }

    private function setDfzNewMemcache() {
        $memcachedfznewConfig = $this->confNamespace . 'Memcachedfznew';
        $this->memcachedfznew = function () use ($memcachedfznewConfig) {
            return $memcachedfznewConfig::instance()->configs();
        };
    }

    private function setHMemcache() {
        $hmemcacheConfig = $this->confNamespace . 'HMemcache';
        $this->hmemcache = function () use ($hmemcacheConfig) {
            return $hmemcacheConfig::instance()->configs();
        };
    }


    private function setRemote() {
        $remoteConfig = $this->confNamespace . 'Remote';
        $this->remote = function () use ($remoteConfig) {
            return $remoteConfig::instance()->configs();
        };
    }

    private function setKafka() {
        $kafkaConfig = $this->confNamespace . 'Kafka';
        $this->kafka = function () use ($kafkaConfig) {
            return $kafkaConfig::instance()->configs();
        };
    }

    private function setScribe() {
        $logConfig = $this->confNamespace . 'Scribe';
        $this->scribe = function () use ($logConfig) {
            return $logConfig::instance()->configs();
        };
    }
    
    private function setSessionRelation() {
        $relation = $this->confNamespace . 'SessionRelationRedis';
        $this->session_relation = function () use ($relation) {
            return $relation::instance()->configs();
        };
    }

    private function setMainSiteRedis() {
        $relation = $this->confNamespace . 'MainSiteRedis';
        $this->main_site_redis = function () use ($relation) {
            return $relation::instance()->configs();
        };
    }

    private function setInternal() {
        $relation = $this->confNamespace . 'Internal';
        $this->passport_app_auth = function () use ($relation) {
            return $relation::instance()->configs();
        };
    }

    private function setIdc() {
        $relation = $this->confNamespace . 'IDCCheck';
        $this->idc_check = function () use ($relation) {
            return $relation::instance()->configs();
        };
    }
}
