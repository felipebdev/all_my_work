import { LambdaCustomResult, ValidateProp } from '../../shared';
import { UserService } from '../service';
import { UserBundle } from '../type/user.interface';
import { Validate } from '../../shared';
import { UsernameDTO } from '../dto';
import { FhirResponse } from '../../fhir/helpers';
import { OperationOutcome } from 'fhir/r4';

export class UserController {
  private readonly userService: UserService;

  constructor() {
    this.userService = new UserService();
  }

  @Validate(UsernameDTO)
  async getUser(
    @ValidateProp() userSearchDTO: UsernameDTO,
  ): Promise<LambdaCustomResult<UserBundle | OperationOutcome>> {
    try {
      const { username } = userSearchDTO;
      const user = await this.userService.getUser(username);
      return {
        statusCode: 200,
        body: user,
      };
    } catch (error) {
      return FhirResponse.fhirErrorResponse('INTERNAL_SERVER_ERROR');
    }
  }
}
