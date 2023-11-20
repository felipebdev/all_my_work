import { LocalAuthGuard } from '@app/auth/guards';
import { AuthService } from '@app/auth/services';
import { Controller, Req, Post, UseGuards } from '@nestjs/common';

@Controller('auth')
export class AuthController {
  constructor(private authService: AuthService) {}

  @UseGuards(LocalAuthGuard)
  @Post('login')
  async login(@Req() req) {
    return this.authService.login(req.user);
  }
}
