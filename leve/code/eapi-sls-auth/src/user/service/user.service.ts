import { CustomError } from '../../shared';
import { CognitoService } from '../../shared/service';
import { UserBundle } from '../type/user.interface';

export class UserService {
  private readonly cognitoService: CognitoService;

  constructor() {
    this.cognitoService = new CognitoService();
  }

  async getUser(username: string): Promise<UserBundle> {
    try {
      const user = await this.cognitoService.findUser(username);

      if (!user) {
        return {
          resourceType: 'Bundle',
          type: 'searchset',
          total: 0,
        };
      }

      const [email, name, phone_number] = ['email', 'name', 'phone_number'].map(
        (attr) => {
          return user.UserAttributes.find(
            (cognito_attr) => cognito_attr.Name === attr,
          ).Value;
        },
      );

      return {
        resourceType: 'Bundle',
        type: 'searchset',
        total: 1,
        entry: [
          {
            resource: {
              resourceType: 'User',
              username: user.Username,
              'status-cognito': user.UserStatus,
              email,
              name,
              phone_number,
            },
          },
        ],
      };
    } catch (error) {
      throw new CustomError('Something went wrong', { code: 500 });
    }
  }
}
