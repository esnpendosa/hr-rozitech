import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:rmih_flutter/main.dart';
import 'package:rmih_flutter/screens/login_screen.dart';

void main() {
  testWidgets('RmihApp displays LoginScreen when no token is present', (WidgetTester tester) async {
    await tester.pumpWidget(const RmihApp(initialToken: null));
    await tester.pumpAndSettle();

    // Pastikan judul RMIH Platform dan tombol Masuk ke Akun ada
    expect(find.text('RMIH Platform'), findsOneWidget);
    expect(find.text('Masuk ke Akun'), findsOneWidget);
  });
}
