import { ConfigModule } from '@app/file-storage/config/config.module'
import { FileStorageModule } from '@app/file-storage/storage.module'
import { INestApplication } from '@nestjs/common'
import { Test, TestingModule, TestingModuleBuilder } from '@nestjs/testing'
import * as fs from 'fs'
import request from 'supertest'

jest.setTimeout(30000)

describe('FileStorageController (e2e)', () => {
  let app: INestApplication
  let fileKey: string
  beforeAll(async () => {
    const moduleBuilder: TestingModuleBuilder = Test.createTestingModule({
      imports: [ConfigModule, FileStorageModule],
      controllers: [],
      providers: []
    })
    const moduleFixture: TestingModule = await moduleBuilder.compile()
    app = moduleFixture.createNestApplication()
    await app.init()
  })
  beforeEach(async () => {
    jest.clearAllMocks()
    await app.init()
    await app.listen(3000)
  })

  afterEach(async () => {
    await app.close()
  })
  afterAll(() => {
    const testUploadDir = 'src/file-storage/data/file-storage/test'
    fs.rm(testUploadDir, { recursive: true }, () => true)
  })

  it('/:appKey (POST)', async () => {
    const response = await request(app.getHttpServer())
      .post('/portal_empresas')
      .set('Accept', 'application/json')
      .attach('file', 'src/file-storage/assets/tests_portal_empresas_import_cs_transactions.csv')
    console.log('response', response)
    fileKey = response.body.fileKey
    expect(response.headers['content-type']).toMatch(/json/)
    expect(response.status).toEqual(201)
    expect(response.body.fileKey).toBeTruthy()
  })
  it('/:appKey (POST) with options', async () => {
    const response = await request(app.getHttpServer())
      .post('/portal_empresas')
      .set('Accept', 'application/json')
      .field('options[notifications][0]', 'importCsvTransactions')
      .attach('file', 'src/file-storage/assets/tests_portal_empresas_import_cs_transactions.csv')
    expect(response.headers['content-type']).toMatch(/json/)
    expect(response.status).toEqual(201)
    expect(response.body.fileKey).toBeTruthy()
  })
  it('should get a previously store ', async () => {
    const response = await request(app.getHttpServer()).get('/portal_empresas/' + fileKey)
    expect(response.status).toEqual(200)
    expect(response.body).toBeTruthy()
  })
})
