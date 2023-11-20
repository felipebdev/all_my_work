export interface JwtPayload {
  iss: string
  iat: number
  exp: number
  aud: string
  sub: string
  platform_id: string
  user_id: string
}
