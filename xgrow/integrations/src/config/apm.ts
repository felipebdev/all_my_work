import apm from 'elastic-apm-node'
import env from './env'
import WinstonLog from '../providers/winston'

const logger = WinstonLog.getInstance()

const { elastic: { apm: { secretToken, serverUrl } }, environment } = env

apm.start({
  serviceName: 'integrations',
  secretToken,
  serverUrl,
  environment: environment
})

logger.debug('APM started.')
