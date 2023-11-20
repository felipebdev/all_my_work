import { Injectable, NotFoundException, UnprocessableEntityException } from '@nestjs/common'
import { GCPStorageService } from '@app/common/services'
import { UserDocumentsDTO } from '@app/store-identity/dto'
import { NextCodeIDExceptionsMessages } from '@app/store-identity/interfaces'
import {
  AbstractBigBoostService,
  AbstractBigIdService,
  AbstractCheckoutAPIService,
  AbstractNextcodeService,
  AbstractSerproService,
  AbstractStoreIdentityService
} from '@app/store-identity/services/abstract'
import { AttemtpsRepository, BankCredentialsRepository, CredentialsRepository } from '@app/common/repositories'

@Injectable()
export class StoreIdentityService implements AbstractStoreIdentityService {
  constructor(
    private readonly bigIdService: AbstractBigIdService,
    private readonly nextcodeService: AbstractNextcodeService,
    private readonly gcpStorageService: GCPStorageService,
    private readonly bigBoostService: AbstractBigBoostService,
    private readonly serproService: AbstractSerproService,
    private readonly checkoutService: AbstractCheckoutAPIService,
    private readonly attemptsRepository: AttemtpsRepository,
    private readonly credentialsRepository: CredentialsRepository,
    private readonly bankCredentialsRepository: BankCredentialsRepository
  ) {}

  async validateDocuments(
    file: Express.Multer.File,
    userDocuments: UserDocumentsDTO,
    correlationId: string,
    { platform_id, user_id }: { platform_id: string; user_id: string }
  ): Promise<boolean> {
    const { document: informedDocument, document_type: documentType } = userDocuments

    const base64File = file.buffer.toString('base64')

    const { fileName } = await this.gcpStorageService.uploadFile(file, 'documents')

    const { id: attemptId } = await this.createAttempt(userDocuments, user_id, fileName)

    const extractedCpf =
      (await this.bigIdService.ocr(base64File)) ?? (await this.nextcodeService.ocr(file.filename, base64File))

    if (documentType === 'cpf') {
      if (informedDocument.replace(/[^\w\s]/gi, '') !== extractedCpf)
        throw new UnprocessableEntityException({
          validate_error: true,
          message: NextCodeIDExceptionsMessages.DIFFERENT_DOCUMENT
        })

      return true
    }

    const bigBoostValidation = await this.bigBoostService.validateRelationship(informedDocument, extractedCpf)

    if (!bigBoostValidation) {
      const personalData = await this.bigBoostService.personalData(extractedCpf)

      const serproValidation = await this.serproService.validateRelationsSerpro(
        informedDocument.replace(/[^\w\s]/gi, ''),
        extractedCpf.replace(/[^\w\s]/gi, ''),
        personalData['Name']
      )

      if (!serproValidation)
        throw new NotFoundException({
          validate_error: true,
          message: `O CNPJ ${informedDocument} não pertence ao CPF ${extractedCpf}`
        })
    }

    await this.checkoutService.createRecipient(platform_id, user_id, correlationId)

    await this.attemptsRepository.preload({
      id: attemptId,
      success: true
    })

    return true
  }

  private async createAttempt(userDocuments: UserDocumentsDTO, userId: string, file: string) {
    const {
      company_name: companyName,
      document,
      first_name: firstName,
      last_name: lastName,
      document_type: typePerson,
      account,
      account_digit: accountDigit,
      account_type: accountType,
      agency,
      agency_digit: agencyDigit,
      bank_code: bankCode,
      legal_name: legalName
    } = userDocuments

    const { id: credentialsId } = await this.credentialsRepository.create({
      companyName,
      datetime: new Date(),
      document,
      file,
      firstName,
      lastName,
      typePerson,
      userId
    })

    await this.bankCredentialsRepository.create({
      account,
      accountDigit,
      accountType,
      agency,
      agencyDigit,
      bankCode,
      credentialsId,
      legalName
    })

    return this.attemptsRepository.create({
      credentialsId,
      success: false
    })
  }
}
