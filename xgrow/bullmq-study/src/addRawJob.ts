import IORedis, {RedisOptions} from 'ioredis'
import dotenv from 'dotenv'
import {v4} from 'uuid'

dotenv.config()

const expoPayload = 
{
  "header": {
    "date": "2023-05-04T13:25:03Z",
    "correlation_id": "4b78c27b-eb57-4a5b-8722-9e675f8783bf",
    "app": {
      "platform_id": "052baabb-284f-45c9-b829-e519ff3bc2fb",
      "event": "anyEvent",
      "action": "bindPushNotification",
      "integration": {
        "type": "expo",
        "metadata": {
          "expoTokens": [
            "ExponentPushToken[ooUdhGMHW9CHd66RbFAA8j]",
            "ExponentPushToken[Gjj2PTLA4iCm1EY3uqbW7d]",
            "ExponentPushToken[Gjj2PTLA4iCm1EY3uqbW7d]",
            "ExponentPushToken[FghGehGSvMCehsrtbFPnTx]"
          ],
          "messageTitle": "Outra tentativa",
          "messageBody": "maisa uma, deve ser enviado somente 1 vez",
          "messageData": {
            
          }
        }
      }
    }
  },
  "payload": {
    "data": [
      
    ]
  }
}

const redisConn: RedisOptions = {
    db: 1,
    host: process.env.REDIS_HOST,
    port: Number(process.env.REDIS_PORT)
}

// const opts = {
//     attempts: process.env.QUEUE_JOB_ATTEMPTS,
//     backoff: {
//         type: 'fixed',
//         delay: Number(process.env.QUEUE_JOB_BACKOFF)
//     }
// }

const addJob = async function () {
    const connection = new IORedis(redisConn)

    await connection.hmset('bull:apps:27', 'data', JSON.stringify(expoPayload))
    // await connection.hmset('bull:apps:27', 'opts', JSON.stringify(opts))
    await connection.zadd('bull:apps:delayed', 1, '27')
}

addJob()
    .then((response) => console.log('addedJob, response: ', response))
    .catch((err) => console.log('error adding job: ', err))
    .finally(() => process.exit(0))



