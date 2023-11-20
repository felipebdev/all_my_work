export interface ISqsPoller {
  handleMessage(message: AWS.SQS.Message)
  onProcessingError(error: Error, message: AWS.SQS.Message)
  onError(error: Error, message: AWS.SQS.Message)
}
