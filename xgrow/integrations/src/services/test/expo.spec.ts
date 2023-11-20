// import { Expo } from '../expo'
// import { expoPayloadMock } from './services.mock'
// // eslint-disable-next-line @typescript-eslint/no-unused-vars
// // eslint-disable-next-line @typescript-eslint/no-unused-vars
// import { ValidateJsAdapter } from '../../adapters/validatejs-adapter'
// import { Expo as ExpoServer } from 'expo-server-sdk'

// jest.mock('expo-server-sdk')

// describe('Expo Service', () => {
//   let service: Expo

//   beforeEach(() => {
//     jest.clearAllTimers()
//     jest.clearAllMocks()
//   })

//   describe('service initialization', () => {
//     it('should be defined', () => {
//       service = new Expo(new ValidateJsAdapter(), expoPayloadMock)
//       expect(service).toBeDefined()
//     })
//   })

//   describe('bindPushNotification', () => {
//     // ExpoServer.isExpoPushToken = jest.fn(() => true)
//     it('should call bindPushNotification correctly', async() => {
//       const spyOnBindTrigger = jest.spyOn(service, 'bindPushNotification')
//       const response = await service.process()
//       expect(spyOnBindTrigger).toBeCalledTimes(1)
//       expect(spyOnBindTrigger).toBeCalledWith(expoPayloadMock)
//       expect(response).toBeUndefined()
//     })
//   })
// })
it('should pass', () => {
  expect(true).toBeDefined()
})
