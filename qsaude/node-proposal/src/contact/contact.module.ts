import { Module } from '@nestjs/common'
import { TypeOrmModule } from '@nestjs/typeorm'
import { ContactEntity } from '@app/contact/entities'
import { ContactController } from '@app/contact/controllers'
import { ContactService } from '@app/contact/services'
import { AbstractContactService } from '@app/contact/services/abstract/contact.service.abstract'

@Module({
  imports: [TypeOrmModule.forFeature([ContactEntity])],
  controllers: [ContactController],
  providers: [{ provide: AbstractContactService, useClass: ContactService }],
  exports: [AbstractContactService]
})
export class ContactModule {}
