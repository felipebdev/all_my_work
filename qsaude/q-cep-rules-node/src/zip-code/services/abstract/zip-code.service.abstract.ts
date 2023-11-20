import { IZipCodeService } from '@app/zip-code/interfaces'

export abstract class AbstractZipCodeService implements IZipCodeService {
  abstract findOne(zipCode: string)
}
