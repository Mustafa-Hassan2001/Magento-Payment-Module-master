ALTER TABLE `{TABLE_PREFIX}payme_transactions`
  ADD CONSTRAINT `payme_transactions_ibfk_1` FOREIGN KEY (`state`) REFERENCES `payme_s_state` (`code`) ON DELETE NO ACTION ON UPDATE NO ACTION;
