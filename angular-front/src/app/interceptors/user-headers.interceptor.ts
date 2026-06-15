import { HttpInterceptorFn } from '@angular/common/http';

export const userHeadersInterceptor: HttpInterceptorFn = (req, next) => {
  const userId    = localStorage.getItem('userId');
  const userEmail = localStorage.getItem('userEmail');

  if (!userId && !userEmail) return next(req);

  const headers: Record<string, string> = {};
  if (userId)    headers['X-User-Id']    = userId;
  if (userEmail) headers['X-User-Email'] = userEmail;

  return next(req.clone({ setHeaders: headers }));
};
