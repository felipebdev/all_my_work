import { SqsProducer } from "@app/sqs/decorators/sqs-producer"
import { createMock } from '@golevelup/nestjs-testing'
import { SqsService } from "@nestjs-packages/sqs"
import { Test } from '@nestjs/testing'


describe('SqsProducerDecorator', () => {
    class DummyClass{

        @SqsProducer('any-queue')
        dummyMethod(data: string){
            return data
        }
    }
    
    const mockSqsService = createMock<SqsService>({
        send: jest.fn(() => ({}))
    })

    let sut: DummyClass

    beforeEach(async ()=>{
        jest.clearAllMocks()
        const moduleRef = await Test.createTestingModule({
            imports:[],
            providers:[
                {
                    provide:'DummyClass',
                    useClass: DummyClass
                },
                {
                    provide: SqsService,
                    useValue: mockSqsService
                }
            ]
        })
        .compile()

        sut = moduleRef.get<DummyClass>('DummyClass')
    })

    describe('SqsProducer', () => {

        it('should produce message through SqsService correctly', async ()=> {
            const spy = jest.spyOn(sut, 'dummyMethod')
            expect(await sut.dummyMethod('dummy')).toBe('dummy')
            expect(spy).toHaveBeenCalledTimes(1)
            expect(spy).toHaveBeenCalledWith('dummy')
            expect(mockSqsService.send).toHaveBeenCalledTimes(1)
            expect(mockSqsService.send).toHaveBeenCalledWith('any-queue',{
                id: '1234',
                body: 'dummy'
            })
        })

        it('should throw an error if SqsService send() fails', async()=> {
            const spy = jest.spyOn(sut, 'dummyMethod')
            jest.spyOn(mockSqsService, 'send').mockImplementation(()=>{
                throw new Error()
            })
            await expect(sut.dummyMethod('dummy')).rejects.toThrow(
                new Error('Somenthing went wrong.')
            )
            expect(spy).toHaveBeenCalledTimes(1)
            expect(spy).toHaveBeenCalledWith('dummy')
            expect(mockSqsService.send).toHaveBeenCalledTimes(1)
            expect(mockSqsService.send).toHaveBeenCalledWith('any-queue',{
                id: '1234',
                body: 'dummy'
            })
        })

    })
})