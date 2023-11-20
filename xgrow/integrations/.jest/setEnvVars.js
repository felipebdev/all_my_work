process.env.APP_ENVIRONMENT='dev'

process.env.REDIS_PORT=6379
process.env.REDIS_HOST='127.0.0.1'
process.env.REDIS_USERNAME='default'
process.env.REDIS_PASSWORD='anypass'
process.env.REDIS_DB='1'
process.env.REDIS_ENABLE_TLS=false
process.env.REDIS_TLS_REJECT_UNAUTHORIZED=false

process.env.QUEUE_NAME='apps'
process.env.QUEUE_JOB_ATTEMPTS=3
process.env.QUEUE_JOB_BACKOFF=3

process.env.MONGO_SRV='mongodb://localhost:27017'
process.env.MONGO_PORT=27017
process.env.MONGO_DB_NAME='xgrow-apps-consumer'
// ṕrocess.env.MONGO_SSL=true
process.env.MONGO_SSL_CA_PATH='anypath'
process.env.ELASTIC_APM_SECRET_TOKEN='anytoken'
process.env.ELASTIC_APM_SERVER_URL='anypath'
