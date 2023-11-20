import {
  CognitoIdentityProviderClient,
  AdminGetUserCommand,
} from '@aws-sdk/client-cognito-identity-provider';
import { AWSConfig, cognitoConfig } from '../config';
import { CognitoUser } from '../types';

export class CognitoService {
  private readonly client: CognitoIdentityProviderClient;
  private readonly UserPoolId: string;
  private readonly ClientId: string;

  constructor() {
    this.client = new CognitoIdentityProviderClient({
      region: AWSConfig.region,
      credentials: {
        accessKeyId: AWSConfig.accessKeyId,
        secretAccessKey: AWSConfig.secretAccessKey,
      },
    });
    this.UserPoolId = cognitoConfig.UserPoolId;
    this.ClientId = cognitoConfig.ClientId;
  }

  async findUser(Username: string): Promise<CognitoUser> {
    const { UserPoolId } = this;
    const findUserCommand = new AdminGetUserCommand({
      UserPoolId,
      Username,
    });

    try {
      const user = await this.client.send(findUserCommand);
      return user as CognitoUser;
    } catch (error) {
      if (error.name === 'UserNotFoundException') {
        return null;
      }
      throw error;
    }
  }
}
