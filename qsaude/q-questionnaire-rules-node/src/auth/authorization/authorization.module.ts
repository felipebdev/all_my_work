import { Module } from '@nestjs/common'
import { AccessControlModule, RolesBuilder } from 'nest-access-control'
import { AbstractRepository } from './repositories/abstract-repository.repository'
import { StorageLocalRepository } from './repositories/storageLocal/storage-local.repository'
import { AbstractRoleService } from './services/abstract-role.service'
import { RoleService } from './services/role-service.service'

@Module({
  imports: [
    AccessControlModule.forRootAsync({
      imports: [],
      inject: [AbstractRoleService],
      useFactory: async (roleService: AbstractRoleService): Promise<RolesBuilder> => {
        return new RolesBuilder(await roleService.getRoles())
      }
    })
  ],
  controllers: [],
  providers: [
    {
      provide: AbstractRoleService,
      useClass: RoleService
    },
    {
      provide: AbstractRepository,
      useClass: StorageLocalRepository
    }
  ]
})
export class AuthorizationModule {}
