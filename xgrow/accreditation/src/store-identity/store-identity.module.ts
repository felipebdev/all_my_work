import { Module } from '@nestjs/common'
import {
  BigIDService,
  NextcodeService,
  StoreIdentityService,
  SerproService,
  CheckoutAPIService
} from '@app/store-identity/services'
import { BigBoostService } from './services/big-boost.service'
import { ConfigModule } from '@nestjs/config'
import { externalServicesConfig } from '@app/store-identity/configs'
import { StoreIdentityController } from '@app/store-identity/controllers'
import { JwtModule } from '@nestjs/jwt'
import {
  AbstractBigBoostService,
  AbstractBigIdService,
  AbstractCheckoutAPIService,
  AbstractNextcodeService,
  AbstractSerproService,
  AbstractStoreIdentityService
} from '@app/store-identity/services/abstract'

@Module({
  providers: [
    { provide: AbstractBigBoostService, useClass: BigBoostService },
    { provide: AbstractNextcodeService, useClass: NextcodeService },
    { provide: AbstractBigIdService, useClass: BigIDService },
    { provide: AbstractStoreIdentityService, useClass: StoreIdentityService },
    { provide: AbstractSerproService, useClass: SerproService },
    { provide: AbstractCheckoutAPIService, useClass: CheckoutAPIService }
  ],
  imports: [
    ConfigModule.forFeature(externalServicesConfig()),
    JwtModule.register({
      secret: process.env.CHECKOUT_JWT_SECRET,
      global: false
    })
  ],
  controllers: [StoreIdentityController]
})
export class StoreIdentityModule {}
