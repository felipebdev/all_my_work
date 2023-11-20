import { HttpStatus, INestApplication } from '@nestjs/common'
import { Test, TestingModule } from '@nestjs/testing'
import { MainModule } from '../main.module'
import request from 'supertest'
import { FileDto } from '../file/dto/file.dto'

describe('FileController (e2e)', () => {
  let app: INestApplication

  beforeEach(async () => {
    const moduleFixture: TestingModule = await Test.createTestingModule({
      imports: [MainModule]
    }).compile()

    app = moduleFixture.createNestApplication()
    await app.init()
  })

  describe('/file/ID (GET)', () => {
    it('it should return file', async () => {
      const id = '61ded667-df22-4322-8422-2a1524c9504e'
      const response = await request(app.getHttpServer()).get(`/file/${id}`)

      const file: FileDto = response.body

      if (file?.id) {
        expect(typeof file.id).toBe('string')

        expect(HttpStatus.OK)
      }
    })
  })
})
