import redis
class RedisCache:
  def __init__(self):    
    self.cache_redis = redis.StrictRedis(host='localhost', port=6379, db=0)    
  

  def getKeyValue(self,key):
    return self.cache_redis.get(key)

  def setKeyValue(self,key,value):
    self.cache_redis.set(key, value)

  def deleteKeyValue(self,key):
    self.cache_redis.delete(key)