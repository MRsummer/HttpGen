//this file is generated by program, you should not change this file
//Created automatically on 15-07-18 01:21:22

#import <Fundation.h>

@interface UserApi
+getUserInfoWithUid:(NSInteger)uid Sid:(NSString *)sid Success:(void (^)(User *))apisuccess Failure:(void (^)(NSString *))apifailure;
+isSingerWithUid:(NSInteger)uid Success:(void (^)(NSInteger))apisuccess Failure:(void (^)(NSString *))apifailure;
@end
