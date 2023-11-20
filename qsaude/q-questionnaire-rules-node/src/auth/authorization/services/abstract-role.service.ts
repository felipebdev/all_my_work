import { IRolesService } from '../interfaces/roles-service.interface'

export abstract class AbstractRoleService implements IRolesService {
  abstract getRoles(): Promise<string[]>
}
