import { Module } from '@nestjs/common'
import { PersonController } from '@app/person/controllers'
import { PersonEntity } from '@app/person/entities'
import { TypeOrmModule } from '@nestjs/typeorm'
import { AbstractPersonService, PersonService } from '@app/person/services'

@Module({
  imports: [TypeOrmModule.forFeature([PersonEntity])],
  controllers: [PersonController],
  providers: [{ provide: AbstractPersonService, useClass: PersonService }]
})
export class PersonModule {}
